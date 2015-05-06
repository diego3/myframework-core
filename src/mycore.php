<?php

use MyFrameWork\Memory\MemoryPage;
use MyFrameWork\Memory\Memory;
/* 
 * Utilities functions to use in PHP
 */


/**
 * Verifica se a string começa com a substring desejada
 * @param string $haystack String original
 * @param string $needle Parte da string que se deseja testar
 * @param boolean $case Define se será realizado dinstinção entre maiuscula ou minuscula
 * @return boolen
 */
function startsWith($haystack, $needle, $case=true) {
    if ($case) {
        return strpos($haystack, $needle, 0) === 0;
    }
    return stripos($haystack, $needle, 0) === 0;
}

/**
 * Verifica se a string termina com a substring desejada
 * @param string $haystack String original
 * @param string $needle Parte da string que se deseja testar
 * @param boolean $case Define se será realizado dinstinção entre maiuscula ou minuscula
 * @return boolen
 */
function endsWith($haystack, $needle, $case=true) {
    $expectedPosition = strlen($haystack) - strlen($needle);
    if ($case) {
        return strrpos($haystack, $needle, 0) === $expectedPosition;
    }
    return strripos($haystack, $needle, 0) === $expectedPosition;
}

/**
 * Remove todas as quebras de linha
 * @param string $str
 * @param char $separator O separador que deverá ser utilizado
 * @return string
 */
function removeLineBreak($str, $separator='') {
    return trim(preg_replace('/^\s*\n\r\s*|\s*\r\n\s*|\s*\n\s*|\s*\r\s*|$/m', $separator, $str));
}

/**
 * Remove os caracteres duplicados de uma string
 * @param string $str
 * @param char $char
 * @return string
 */
function removeDuplicatedChar($str, $char=' ') {
    if ($char == ' ') { 
        $aux = '\s';
    }
    else if (in_array($char, array('.', '?', '(', ')', '[', ']', '{', '}', '/', '\\', '*', '%', '$', '#', '@', '+', '-'))) {
        $aux = '\\' . $char;
    }
    else {
        $aux = $char;
    }
    return preg_replace('/' . $aux . '+/', $char, $str);  
}

/**
 * Retorna o valor de um array a partir de uma chave, se o array não possui a chave retorna o valor default
 * @param array $array O vetor que contem todos os valores
 * @param string $key A chave que será buscada no vetor
 * @param mixed $default O valor padrão que será utiliado caso o vetor não possua o valor. O padrão é null
 * @return mixed
 */
function getValueFromArray($array, $key, $default=null) {
    if (isset($array[$key])) {
        return $array[$key];
    }
    return $default;
}

/**
 * Truncate decimal
 * @param float $number
 * @param int $decimals
 * @return float
 */
function truncateDecimal($number, $decimals) {
    if ($decimals < 0) {
        return $number;
    }
    $strval = strval($number);
    $point_index = strrpos($strval, '.');
    return floatval(substr($strval, 0, $point_index + $decimals + 1));
}

/**
 * Verifica se um email é valido e opcionalmente checa se o domínio existe
 * 
 * @param string $email
 * @param boolean $checkDNS
 * @return boolean
 */
function isValidEmail($email, $checkDNS = false) {
    $valid = (
        /* Preference for native version of function */
        function_exists('filter_var') and filter_var($email, FILTER_VALIDATE_EMAIL)
    ) || (
        /* The maximum length of an e-mail address is 320 octets, per RFC 2821. */
        strlen($email) <= 320
        /*
         * The regex below is based on a regex by Michael Rushton.
         * However, it is not identical. I changed it to only consider routeable
         * addresses as valid. Michael's regex considers a@b a valid address
         * which conflicts with section 2.3.5 of RFC 5321 which states that:
         *
         * Only resolvable, fully-qualified domain names (FQDNs) are permitted
         * when domain names are used in SMTP. In other words, names that can
         * be resolved to MX RRs or address (i.e., A or AAAA) RRs (as discussed
         * in Section 5) are permitted, as are CNAME RRs whose targets can be
         * resolved, in turn, to MX or address RRs. Local nicknames or
         * unqualified names MUST NOT be used.
         *
         * This regex does not handle comments and folding whitespace. While
         * this is technically valid in an email address, these parts aren't
         * actually part of the address itself.
         */
        and preg_match_all(
            '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?))'.
            '{255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?))'.
            '{65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|'.
            '(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))'.
            '(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|'.
            '(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|'.
            '(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})'.
            '(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126})'.'{1,}'.
            '(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|'.
            '(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|'.
            '(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::'.
            '(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|'.
            '(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|'.
            '(?:(?!(?:.*[a-f0-9]:){5,})'.'(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::'.
            '(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|'.
            '(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|'.
            '(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD', $email)
    );

    if ($valid) {
        if ($checkDNS && ($domain = end(explode('@',$email, 2)))) {
            /*
            Note:
            Adding the dot enforces the root.
            The dot is sometimes necessary if you are searching for a fully qualified domain
            which has the same name as a host on your local domain.
            Of course the dot does not alter results that were OK anyway.
            */
            return checkdnsrr($domain . '.', 'MX');
        }
        return true;
    }
    return false;
}

/**
 * Verifica se um array é associativo.
 * Se o valor informado não for um array retorna false. Arrays vazios também retornam false
 * @param array $array
 * @return boolean
 */
function is_assoc($array) {
    if (is_array($array)) {
        $t = count($array);
        return $t > 0 && count(array_filter(array_keys($array), 'is_string')) == $t;
    }
    return false;
}

/**
 * Realiza uma chamada POST/GET para 
 * @param string $method Método GET ou POST
 * @param string $url URL que deverá ser chamada
 * @param array $data Parametros da requisição
 * @param string $output Formato da resposta (html ou json)
 * @return mixed
 */
function httpRequest($method, $url, $data=array(), $output='html') {
    $ch = curl_init();

    if (strtolower($method) == 'post') {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); 
    }
    else {
        $url .= '?' . http_build_query($data);
    }
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//fix 301 http code
    
    $content = curl_exec($ch);
    curl_close($ch);
    
    if( !$content ) {
       throw new \Exception("Curl error : " . curl_error($ch));
    }
    switch (strtolower($output)) {
        case 'xml':
            $xml = simplexml_load_string($content);
            if($xml === false) {
                return false;
            }
            $content = json_encode($xml);
        case 'json':
            return json_decode($content, true);
        default:
            return $content;
    }
}

function httpPOST($url, $data, $output='html') {
    return httpRequest('post', $url, $data, $output);
}

function httpGET($url, $data=array(), $output) {
    return httpRequest('get', $url, $data, $output);
}

// @codeCoverageIgnoreStart
/**
 * Redireciona o fluxo para uma nova URL
 * Se uma $message for informada a mesma será exibida em $time se
 * @param string $url URL
 * @param string $message [optional]
 * @param int $time [optional]
 */
function redirect($url, $message='', $time=0) {
    if (!startsWith($url, 'http', true)) {
        $url = DOMAIN . (startsWith($url, '/') ? substr($url, 1) : $url);
    }
    
    if ($time == 0) {
        header('Location: ' . $url);
        echo "<script>location.href='{$url}';</script>";
        exit;
    }
    else {
        //TODO
    }
}
// @codeCoverageIgnoreEnd

function debug($on=true) {
    MemoryPage::add('debug', $on);
}

/**
 * Replace accented characters with non accented
 *
 * @param $str
 * @return mixed
 * @link http://myshadowself.com/coding/php-function-to-convert-accented-characters-to-their-non-accented-equivalant/
 */
function removeAccents($str) {
    $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', 'Ώ', 'ώ', 'Ί', 'ί', 'ϊ', 'ΐ', 'Ύ', 'ύ', 'ϋ', 'ΰ', 'Ή', 'ή');
    $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η');
    return str_replace($a, $b, $str);
}

/**
 * Used to debug in production mode
 * @return bool if local or not
 */
function hostFromAlpha() {
    return ($_SERVER["REMOTE_ADDR"] == "200.233.109.163" or $_SERVER["REMOTE_ADDR"] == "200.233.109.162");
}

/**
 * var_dump com saída formatada
 * @param mixed $var 
 */
function dump($var){
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
}

function sysout($log_text) {
    $path = str_replace("/", "\\", PATH_LOCAL . "/sysout.log");
    file_put_contents($path, $log_text, FILE_APPEND);
}