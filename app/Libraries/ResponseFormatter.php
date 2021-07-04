<?php

namespace App\Libraries;

class ResponseFormatter
{
    /**
     * API Response
     *
     * @var array
     */
    protected static $response = [
        'meta' => [
            'code' => \CodeIgniter\HTTP\Response::HTTP_OK,
            'status' => 'success',
            'message' => null,
        ],
        'data' => null,
    ];

    /**
     * Give success response.
     */
    public static function success($data = null, $message = null, $code = \CodeIgniter\HTTP\Response::HTTP_OK)
    {
        self::$response['meta']['message'] = $message;
        self::$response['data'] = $data;

        header('Content-Type: application/json');
        header('Status: ' . $code);
        return json_encode(self::$response);
    }

    /**
     * Give error response.
     */
    public static function error($message = null, $code = \CodeIgniter\HTTP\Response::HTTP_BAD_REQUEST)
    {
        self::$response['meta']['code'] = $code;
        self::$response['meta']['status'] = 'error';
        self::$response['meta']['message'] = $message;
        
        header('Content-Type: application/json');
        header('Status: ' . $code);
        return json_encode(self::$response);
    }
}
