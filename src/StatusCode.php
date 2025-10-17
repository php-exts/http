<?php
declare (strict_types = 1);

namespace Zeus\Http;

/**
 * Http Status Code
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status
 * @see https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
 * @author imxieke <oss@live.hk>
 * @copyright (c) 2025 CloudFlying
 * @date 2025/10/17 17:16:44
 */
trait StatusCode
{
    // Informational 1xx
    public const int HTTP_CONTINUE = 100;
    public const int HTTP_SWITCHING_PROTOCOLS = 101;
    public const int HTTP_PROCESSING = 102;            // RFC2518
    public const int HTTP_EARLY_HINTS = 103;           // RFC8297

    // Successful 2xx
    public const int HTTP_OK = 200;
    public const int HTTP_CREATED = 201;
    public const int HTTP_ACCEPTED = 202;
    public const int HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    public const int HTTP_NO_CONTENT = 204;
    public const int HTTP_RESET_CONTENT = 205;
    public const int HTTP_PARTIAL_CONTENT = 206;
    public const int HTTP_MULTI_STATUS = 207;          // RFC4918
    public const int HTTP_ALREADY_REPORTED = 208;      // RFC5842
    public const int HTTP_IM_USED = 226;               // RFC3229

    // Redirection 3xx
    public const int HTTP_MULTIPLE_CHOICES = 300;
    public const int HTTP_MOVED_PERMANENTLY = 301;
    public const int HTTP_FOUND = 302;
    public const int HTTP_SEE_OTHER = 303;
    public const int HTTP_NOT_MODIFIED = 304;
    public const int HTTP_USE_PROXY = 305;
    public const int HTTP_RESERVED = 306;
    public const int HTTP_TEMPORARY_REDIRECT = 307;
    public const int HTTP_PERMANENTLY_REDIRECT = 308;  // RFC7238

    // Client Errors 4xx
    public const int HTTP_BAD_REQUEST = 400;
    public const int HTTP_UNAUTHORIZED = 401;
    public const int HTTP_PAYMENT_REQUIRED = 402;
    public const int HTTP_FORBIDDEN = 403;
    public const int HTTP_NOT_FOUND = 404;
    public const int HTTP_METHOD_NOT_ALLOWED = 405;
    public const int HTTP_NOT_ACCEPTABLE = 406;
    public const int HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    public const int HTTP_REQUEST_TIMEOUT = 408;
    public const int HTTP_CONFLICT = 409;
    public const int HTTP_GONE = 410;
    public const int HTTP_LENGTH_REQUIRED = 411;
    public const int HTTP_PRECONDITION_FAILED = 412;
    public const int HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    public const int HTTP_REQUEST_URI_TOO_LONG = 414;
    public const int HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    public const int HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    public const int HTTP_EXPECTATION_FAILED = 417;
    public const int HTTP_I_AM_A_TEAPOT = 418;                                               // RFC2324
    public const int HTTP_MISDIRECTED_REQUEST = 421;                                         // RFC7540
    public const int HTTP_UNPROCESSABLE_ENTITY = 422;                                        // RFC4918
    public const int HTTP_LOCKED = 423;                                                      // RFC4918
    public const int HTTP_FAILED_DEPENDENCY = 424;                                           // RFC4918
    public const int HTTP_TOO_EARLY = 425;                                                   // RFC-ietf-httpbis-replay-04
    public const int HTTP_UPGRADE_REQUIRED = 426;                                            // RFC2817
    public const int HTTP_PRECONDITION_REQUIRED = 428;                                       // RFC6585
    public const int HTTP_TOO_MANY_REQUESTS = 429;                                           // RFC6585
    public const int HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;                             // RFC6585
    public const int HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;                               // RFC7725

    // Server Errors 5xx
    public const int HTTP_INTERNAL_SERVER_ERROR = 500;
    public const int HTTP_NOT_IMPLEMENTED = 501;
    public const int HTTP_BAD_GATEWAY = 502;
    public const int HTTP_SERVICE_UNAVAILABLE = 503;
    public const int HTTP_GATEWAY_TIMEOUT = 504;
    public const int HTTP_VERSION_NOT_SUPPORTED = 505;
    public const int HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;                        // RFC2295
    public const int HTTP_INSUFFICIENT_STORAGE = 507;                                        // RFC4918
    public const int HTTP_LOOP_DETECTED = 508;                                               // RFC5842
    public const int HTTP_NOT_EXTENDED = 510;                                                // RFC2774
    public const int HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;                             // RFC6585

    /**
     * Status Text
     *
     * @var array
     * @author CloudFlying
     * @date 2025/10/17 17:19:21
     */
    public static array $statusTexts = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',            // RFC2518
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',          // RFC4918
        208 => 'Already Reported',      // RFC5842
        226 => 'IM Used',               // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',    // RFC7238
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Content Too Large',                                           // RFC-ietf-httpbis-semantics
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',                                               // RFC2324
        421 => 'Misdirected Request',                                         // RFC7540
        422 => 'Unprocessable Content',                                       // RFC-ietf-httpbis-semantics
        423 => 'Locked',                                                      // RFC4918
        424 => 'Failed Dependency',                                           // RFC4918
        425 => 'Too Early',                                                   // RFC-ietf-httpbis-replay-04
        426 => 'Upgrade Required',                                            // RFC2817
        428 => 'Precondition Required',                                       // RFC6585
        429 => 'Too Many Requests',                                           // RFC6585
        431 => 'Request Header Fields Too Large',                             // RFC6585
        451 => 'Unavailable For Legal Reasons',                               // RFC7725
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',                                     // RFC2295
        507 => 'Insufficient Storage',                                        // RFC4918
        508 => 'Loop Detected',                                               // RFC5842
        510 => 'Not Extended',                                                // RFC2774
        511 => 'Network Authentication Required',                             // RFC6585
    ];

    protected string $content;
    protected string $version;
    protected int $statusCode;
    protected string $statusText;
    protected ?string $charset = null;

    public function __construct(int $statusCode = 200)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * Get Http Status Code Message
     *
     * @param int $code
     * @return mixed|string
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 17:20:52
     */
    public function getMessageFromCode(int $code)
    {
        return $this->statusTexts[$code] ?? 'Unknown Status Code';
    }

    /**
     * Response Is Invalid?
     *
     * @see https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
     * @return bool
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 17:01:06
     */
    public function isInvalid(): bool
    {
        return $this->statusCode < 100 || $this->statusCode >= 600;
    }

    /**
     * is Infomation ?
     * @return bool
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 17:02:16
     */
    public function isInformational(): bool
    {
        return $this->statusCode >= 100 && $this->statusCode < 200;
    }

    /**
     * Is Response Successful ?
     *
     * @return bool
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 17:02:53
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * Is Redirect ?
     *
     * @return bool
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 17:03:25
     */
    public function isRedirection(): bool
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    /**
     * Is Client Error ?
     *
     * @return bool
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 17:03:44
     */
    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * Is Server Error ?
     *
     * @return bool
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 17:04:00
     */
    public function isServerError(): bool
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }

    /**
     * Response Is OK ?
     *
     * @return bool
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 17:04:18
     */
    public function isOk(): bool
    {
        return 200 === $this->statusCode;
    }

    /**
     * Response Is Forbdiden ?
     *
     * @return bool
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 17:04:35
     */
    public function isForbidden(): bool
    {
        return 403 === $this->statusCode;
    }

    /**
     * Page Is Not Found ?
     *
     * @return bool
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 17:04:59
     */
    public function isNotFound(): bool
    {
        return 404 === $this->statusCode;
    }

    /**
     * Is Redirect ?
     * @param mixed $location
     * @return bool
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 17:05:29
     */
    public function isRedirect(?string $location = null): bool
    {
        return \in_array($this->statusCode, [201, 301, 302, 303, 307, 308]);
    }

    /**
     * Response Is Empty ?
     *
     * @return bool
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 17:06:17
     */
    public function isEmpty(): bool
    {
        return \in_array($this->statusCode, [204, 304]);
    }
}
