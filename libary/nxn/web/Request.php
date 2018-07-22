<?php
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nxn\web;

/**
 * Request represents an HTTP request.
 *
 * The methods dealing with URL accept / return a raw path (% encoded):
 *   * getBasePath
 *   * getBaseUrl
 *   * getPathInfo
 *   * getRequestUri
 *   * getUri
 *   * getUriForPath
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Request
{
    const HEADER_FORWARDED = 'forwarded';
    const HEADER_CLIENT_IP = 'client_ip';
    const HEADER_CLIENT_HOST = 'client_host';
    const HEADER_CLIENT_PROTO = 'client_proto';
    const HEADER_CLIENT_PORT = 'client_port';

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_HEAD = 'HEAD';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_PATCH = 'PATCH';
    const METHOD_TRACE = 'TRACE';
    const METHOD_CONNECT = 'CONNECT';

    /**
     * @var \nxn\validate\Validate
     */
    public $validator;
    /**
     * Custom parameters.
     */
    public $attributes;

    /**
     * Request body parameters ($_POST).
     */
    public $request;

    /**
     * Query string parameters ($_GET).
     */
    public $query;

    /**
     * Server and execution environment parameters ($_SERVER).
     */
    public $server;

    /**
     * Uploaded files ($_FILES).
     */
    public $files;

    /**
     * Cookies ($_COOKIE).
     */
    public $cookies;

    /**
     * Headers (taken from the $_SERVER).
     */
    public $headers;

    /**
     * @var array
     */
    protected $content;

    /**
     * @var array
     */
    protected $charsets;

    /**
     * @var array
     */
    protected $encodings;

    /**
     * @var array
     */
    protected $acceptableContentTypes;


    /**
     * @var string
     */
    protected $requestUri;

    /**
     * @var string
     */
    protected $baseUrl;


    /**
     * @var string
     */
    protected $method;
    protected $port = 80;
    protected $scheme = 'HTTP/1.1';


    /**
     * Constructor.
     *
     * @param array $query The GET parameters
     * @param array $request The POST parameters
     * @param array $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array $cookies The COOKIE parameters
     * @param array $files The FILES parameters
     * @param array $server The SERVER parameters
     * @param string|resource $content The raw body data
     */
    public function __construct()
    {
        $this->initialize($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER, []);
    }

    /**
     * Sets the parameters for this request.
     *
     * This method also re-initializes all properties.
     *
     * @param array $query The GET parameters
     * @param array $request The POST parameters
     * @param array $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array $cookies The COOKIE parameters
     * @param array $files The FILES parameters
     * @param array $headers The SERVER parameters
     * @param string|resource $content The raw body data
     */
    public function initialize(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $headers = array(), $content = null)
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->requestUri = $_SERVER['REQUEST_URI'];
        //  Undefined index: Cookie in /home/www/www.note.com/libary/nxn/web/Request.ph
        $headers = ['Cookie' => ''];

        // Cookie: PHPSESSID=7h60fpf3cbom42gh0di1oglb0h; makeLifeEasier=hriqfaq4oepur71pjufed24gda
        foreach ($_COOKIE as $name => $value) {
            if (isset($headers['Cookie'][0])) {
                $headers['Cookie'] .= ';' . $name . '=' . $value;
            } else {
                $headers['Cookie'] .= $name . '=' . $value;
            }
        }

        //Host: 60.205.214.153:88
        $components = parse_url($_SERVER['REQUEST_URI']);

        if (isset($components['scheme'])) {
            $this->scheme = $components['scheme'];
            if ('https' === $components['scheme']) {
                $this->port = 443;
            } else {
                $this->port = 80;
            }
        }

        if (isset($components['host'])) {
            $header['HOST'] = $this->port != 80 ? ($components['host'] . ':' . $this->port) : $components['host'];
        }
        //@todo 补充其他 header
        $this->request = $request;
        $this->query = $query;
        $this->attributes = $attributes;
        $this->cookies = $cookies;
        $this->files = $files;
        $this->headers = $headers;
        $this->content = $this->getContent();
        $this->languages = null;
        $this->charsets = null;
        $this->encodings = null;
        $this->validator = new \nxn\validate\Validate();
        // set data from $_REQUEST, 这样保 GET 部分的入参也被加入进来
        $this->validator->setData($_REQUEST);
    }


    /**
     * 将 validate 类暂时整合到 request 对象中,方便对接口进行参数校验
     *
     * @todo 将AR模型中的参数校验去掉, 上提到 controller 这一层在这, 将该类放到request里边
     */
    public function validate($attributesAndRules, $descMap = [])
    {
        $this->validator->attributeDesction = $descMap;
        if ($this->validator->setRules($attributesAndRules)->failed()) {
            Ajax::json(0, $this->validator->fails(), 'validate error');
        };
    }




    public function getContent()
    {
        if (!isset($this->content)) {
            $contentType = isset($_SERVER['HTTP_CONTENT_TYPE']) ? $_SERVER['HTTP_CONTENT_TYPE'] : '';
            if (0 === strpos($contentType, 'application/x-www-form-urlencoded')
                && in_array(strtoupper($_SERVER['REQUEST_METHOD']), ['PUT', 'DELETE', 'PATCH'])
            ) {
                $rawData = file_get_contents('php://input');
                mb_parse_str($rawData, $data);
                // 只是为了兼容早期代码,后期考虑移除
                $_REQUEST = $data;

                $this->content = array_merge($_REQUEST, $data);
            }
            $this->content = $_POST;
        }
        return $this->content;
    }


    /**
     * Returns the request as a string.
     *
     * @return string The request
     */
    public function __toString()
    {
        try {
            $content = $this->getContent();
        } catch (\LogicException $e) {
            return trigger_error($e, E_USER_ERROR);
        };

        return
            sprintf('%s %s %s', $this->method, $this->requestUri, $this->scheme) . "\r\n" .
            $this->headers . "\r\n" .
            http_build_query($content);
    }

}
