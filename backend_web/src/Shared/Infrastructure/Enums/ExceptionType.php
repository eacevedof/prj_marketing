<?php

namespace App\Shared\Infrastructure\Enums;

abstract class ExceptionType
{
    const CODE_CONTINUE = 100;
    const CODE_SWITCHING_PROTOCOLS = 101;
    const CODE_PROCESSING = 102;

    const CODE_OK = 200;
    const CODE_CREATED = 201;
    const CODE_ACCEPTED = 202;  //para procesos en background
    const CODE_NON_AUTHORITATIVE_INFORMATION = 203;
    const CODE_NO_CONTENT = 204;
    const CODE_RESET_CONTENT = 205;
    const CODE_PARTIAL_CONTENT = 206;
    const CODE_MULTI_STATUS = 207;
    const CODE_ALREADY_REPORTED = 208;
    const CODE_IM_USED = 226;

    const CODE_MULTIPLE_CHOICES = 300;
    const CODE_MOVED_PERMANENTLY = 301;
    const CODE_FOUND = 302;
    const CODE_SEE_OTHER = 303;
    const CODE_NOT_MODIFIED = 304;
    const CODE_USE_PROXY = 305;
    const CODE_SWITCH_PROXY = 306;
    const CODE_TEMPORARY_REDIRECT = 307;
    const CODE_PERMANENT_REDIRECT = 308;

    const CODE_BAD_REQUEST = 400;
    const CODE_UNAUTHORIZED = 401;
    const CODE_PAYMENT_REQUIRED = 402;
    const CODE_FORBIDDEN = 403;
    const CODE_NOT_FOUND = 404;
    const CODE_METHOD_NOT_ALLOWED = 405;
    const CODE_NOT_ACCEPTABLE = 406;
    const CODE_PROXY_AUTHENTICATION_REQUIRED = 407;
    const CODE_REQUEST_TIMEOUT = 408;
    const CODE_CONFLICT = 409;
    const CODE_GONE = 410;
    const CODE_LENGTH_REQUIRED = 411;
    const CODE_PRECONDITION_FAILED = 412;
    const CODE_REQUEST_ENTITY_TOO_LARGE = 413;
    const CODE_REQUEST_URI_TOO_LONG = 414;
    const CODE_UNSUPPORTED_MEDIA_TYPE = 415;
    const CODE_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const CODE_EXPECTATION_FAILED = 417;
    const CODE_IM_A_TEAPOT = 418;
    const CODE_AUTHENTICATION_TIMEOUT = 419;
    const CODE_ENHANCE_YOUR_CALM = 420;
    const CODE_METHOD_FAILURE = 420;
    const CODE_UNPROCESSABLE_ENTITY = 422;
    const CODE_LOCKED = 423;
    const CODE_FAILED_DEPENDENCY = 424;
    const CODE_UNORDERED_COLLECTION = 425;
    const CODE_UPGRADE_REQUIRED = 426;
    const CODE_PRECONDITION_REQUIRED = 428;
    const CODE_TOO_MANY_REQUESTS = 429;
    const CODE_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    const CODE_NO_RESPONSE = 444;
    const CODE_RETRY_WITH = 449;
    const CODE_BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS = 450;
    const CODE_REDIRECT = 451;
    const CODE_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    const CODE_REQUEST_HEADER_TOO_LARGE = 494;
    const CODE_CERT_ERROR = 495;
    const CODE_NO_CERT = 496;
    const CODE_HTTP_TO_HTTPS = 497;
    const CODE_CLIENT_CLOSED_REQUEST = 499;

    const CODE_INTERNAL_SERVER_ERROR = 500;
    const CODE_NOT_IMPLEMENTED = 501;
    const CODE_BAD_GATEWAY = 502;
    const CODE_SERVICE_UNAVAILABLE = 503;
    const CODE_GATEWAY_TIMEOUT = 504;
    const CODE_HTTP_VERSION_NOT_SUPPORTED = 505;
    const CODE_VARIANT_ALSO_NEGOTIATES = 506;
    const CODE_INSUFFICIENT_STORAGE = 507;
    const CODE_LOOP_DETECTED = 508;
    const CODE_BANDWIDTH_LIMIT_EXCEEDED = 509;
    const CODE_NOT_EXTENDED = 510;
    const CODE_NETWORK_AUTHENTICATION_REQUIRED = 511;
    const CODE_NETWORK_READ_TIMEOUT_ERROR = 598;
    const CODE_NETWORK_CONNECT_TIMEOUT_ERROR = 599;
}
