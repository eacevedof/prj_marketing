<?php

namespace App\Shared\Infrastructure\Enums;

abstract class ResponseType
{
    const CONTINUE = 100;
    const SWITCHING_PROTOCOLS = 101;
    const PROCESSING = 102;

    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;  //para procesos en background
    const NON_AUTHORITATIVE_INFORMATION = 203;
    const NO_CONTENT = 204;
    const RESET_CONTENT = 205;
    const PARTIAL_CONTENT = 206;
    const MULTI_STATUS = 207;
    const ALREADY_REPORTED = 208;
    const IM_USED = 226;

    const MULTIPLE_CHOICES = 300;
    const MOVED_PERMANENTLY = 301;
    const FOUND = 302;
    const SEE_OTHER = 303;
    const NOT_MODIFIED = 304;
    const USE_PROXY = 305;
    const SWITCH_PROXY = 306;
    const TEMPORARY_REDIRECT = 307;
    const PERMANENT_REDIRECT = 308;

    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const PAYMENT_REQUIRED = 402;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const NOT_ACCEPTABLE = 406;
    const PROXY_AUTHENTICATION_REQUIRED = 407;
    const REQUEST_TIMEOUT = 408;
    const CONFLICT = 409;
    const GONE = 410;
    const LENGTH_REQUIRED = 411;
    const PRECONDITION_FAILED = 412;
    const REQUEST_ENTITY_TOO_LARGE = 413;
    const REQUEST_URI_TOO_LONG = 414;
    const UNSUPPORTED_MEDIA_TYPE = 415;
    const REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const EXPECTATION_FAILED = 417;
    const IM_A_TEAPOT = 418;
    const AUTHENTICATION_TIMEOUT = 419;
    const ENHANCE_YOUR_CALM = 420;
    const METHOD_FAILURE = 420;
    const UNPROCESSABLE_ENTITY = 422;
    const LOCKED = 423;
    const FAILED_DEPENDENCY = 424;
    const UNORDERED_COLLECTION = 425;
    const UPGRADE_REQUIRED = 426;
    const PRECONDITION_REQUIRED = 428;
    const TOO_MANY_REQUESTS = 429;
    const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    const NO_RESPONSE = 444;
    const RETRY_WITH = 449;
    const BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS = 450;
    const REDIRECT = 451;
    const UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    const REQUEST_HEADER_TOO_LARGE = 494;
    const CERT_ERROR = 495;
    const NO_CERT = 496;
    const HTTP_TO_HTTPS = 497;
    const CLIENT_CLOSED_REQUEST = 499;

    const INTERNAL_SERVER_ERROR = 500;
    const NOT_IMPLEMENTED = 501;
    const BAD_GATEWAY = 502;
    const SERVICE_UNAVAILABLE = 503;
    const GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    const VARIANT_ALSO_NEGOTIATES = 506;
    const INSUFFICIENT_STORAGE = 507;
    const LOOP_DETECTED = 508;
    const BANDWIDTH_LIMIT_EXCEEDED = 509;
    const NOT_EXTENDED = 510;
    const NETWORK_AUTHENTICATION_REQUIRED = 511;
    const NETWORK_READ_TIMEOUT_ERROR = 598;
    const NETWORK_CONNECT_TIMEOUT_ERROR = 599;
    
    const HEADERS = [
        100 => "HTTP/1.1 100 Continue",
        101 => "HTTP/1.1 101 Switching Protocols",
        200 => "HTTP/1.1 200 OK",
        201 => "HTTP/1.1 201 Created",
        202 => "HTTP/1.1 202 Accepted",
        203 => "HTTP/1.1 203 Non-Authoritative Information",
        204 => "HTTP/1.1 204 No Content",
        205 => "HTTP/1.1 205 Reset Content",
        206 => "HTTP/1.1 206 Partial Content",
        300 => "HTTP/1.1 300 Multiple Choices",
        301 => "HTTP/1.1 301 Moved Permanently",
        302 => "HTTP/1.1 302 Found",
        303 => "HTTP/1.1 303 See Other",
        304 => "HTTP/1.1 304 Not Modified",
        305 => "HTTP/1.1 305 Use Proxy",
        307 => "HTTP/1.1 307 Temporary Redirect",
        400 => "HTTP/1.1 400 Bad Request",
        401 => "HTTP/1.1 401 Unauthorized",
        402 => "HTTP/1.1 402 Payment Required",
        403 => "HTTP/1.1 403 Forbidden",
        404 => "HTTP/1.1 404 Not Found",
        405 => "HTTP/1.1 405 Method Not Allowed",
        406 => "HTTP/1.1 406 Not Acceptable",
        407 => "HTTP/1.1 407 Proxy Authentication Required",
        408 => "HTTP/1.1 408 Request Time-out",
        409 => "HTTP/1.1 409 Conflict",
        410 => "HTTP/1.1 410 Gone",
        411 => "HTTP/1.1 411 Length Required",
        412 => "HTTP/1.1 412 Precondition Failed",
        413 => "HTTP/1.1 413 Request Entity Too Large",
        414 => "HTTP/1.1 414 Request-URI Too Large",
        415 => "HTTP/1.1 415 Unsupported Media Type",
        416 => "HTTP/1.1 416 Requested Range Not Satisfiable",
        417 => "HTTP/1.1 417 Expectation Failed",
        500 => "HTTP/1.1 500 Internal Server Error",
        501 => "HTTP/1.1 501 Not Implemented",
        502 => "HTTP/1.1 502 Bad Gateway",
        503 => "HTTP/1.1 503 Service Unavailable",
        504 => "HTTP/1.1 504 Gateway Time-out",
        505 => "HTTP/1.1 505 HTTP Version Not Supported",
    ];
}
