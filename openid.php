<?php

if ((include_once 'config.php') == FALSE) require_once 'config.example.php';
require_once 'jwt.php';

class OpenID {
    private array $openid_config;
    private array $openid_discovery;
    private string $email;
    private string $provider;

    // Initializer
    public function __construct(string $email) {
        // Load configs.
        $config = $GLOBALS['config'];

        // Parse Email address to extract its domain name.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception('Please input a valid Email address.');

        $domain = array_pop(explode('@', $email));
        $provider = $config['emails'][$domain];
        if (empty($provider)) $provider = $this->dns_mx_resolve($domain);
        if (empty($provider)) throw new Exception('We currently do not support logging in with @' . $domain . ' email addresses.');

        $this->openid_config = $config['providers'][$provider];
        if (empty($this->openid_config)) throw new Exception('BUG: Provider configuration for ' . $provider . ' missing.');
        $this->provider = $provider;

        // Get the current Google authentication entry point.
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_URL, $this->openid_config['configuration']);
        $openid_json = curl_exec($curl);
        curl_close($curl);
        $this->openid_discovery = json_decode($openid_json, true);
        if (empty($this->openid_discovery)) throw new Exception('BUG: Provider configuration can not be loaded from $this->openid_config["configuration"].');

        $this->email = $email;
    }

    private function dns_mx_resolve(string $domain) : string {
        // Load configs.
        $config = $GLOBALS['config'];

        // Query MX records.
        $hosts = array();
        $weights = array();
        getmxrr($domain, $hosts, $weights);
        
        // Sort the MX records by priority.
        $combined = array_combine($hosts, $weights);
        asort($combined);
        $hosts = array_keys($combined);

        // Enumerate throught the hosts
        foreach ($hosts as &$host) {
            for ($host_segment = $host; !empty(host_segment); $host_segment = substr(strstr($host_segment, '.'), 1)) {
                if (array_key_exists($host_segment, $config['mx'])) return $config['mx'][$host_segment];
            }
        }

        return '';
    }

    public function authorization_url(string $csrf, string $extras = '') : string {
        return $this->openid_discovery['authorization_endpoint'] . '?' .
        'response_type=' . $this->openid_config['response-type'] . '&' .
        'client_id=' . $this->openid_config['client-id'] . '&' .
        'scope=openid%20email&' .
        'redirect_uri=https%3a//' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/auth.php&' .
        'nonce=' . $csrf . '&' .
        'state=' . $csrf . '&' .
        (!empty($this->openid_config['response-mode']) ? 'response_mode=' . $this->openid_config['response-mode'] . '&' : '') .
        'login_hint=' . $email .
        (!empty($extras) ? '&' . $extras : '');
    }

    public function check_email(string $email) : bool {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->email == $email;
        } else {
            return false;
        }
    }

    public function code2token(string $code) : string {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_URL, $this->openid_discovery['token_endpoint']);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS,
            'code='. $code . '&' .
            'client_id=' . $this->openid_config['client-id'] . '&' .
            'client_secret=' . $this->openid_config['client-secret'] . '&' .
            'redirect_uri=https%3a//' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0] . '&' .
            'grant_type=authorization_code'
        );
        $tokens_json = curl_exec($curl);
        $tokens = json_decode($tokens_json, true);
        return $tokens['id_token'];
    }

    public function token_endpoint() : string {
        return $this->openid_config['token_endpoint'];
    }

    public function provider() : string {
        return $this->provider;
    }

    public function recovery() {
        return $this->openid_config['login-recovery'];
    }
}

?>
