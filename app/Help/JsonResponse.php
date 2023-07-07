<?php

/**
 * File JsonResponse.php
 *
 * @author Edward Chung <leonardo071123@gmail.com>
 * @version 1.0
 */
namespace App\Help;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * Class JsonResponse
 * Simple response object for Laravue application
 * Response format:
 * {
 *   'code': 0,
 *   'data': [],
 *   'message': ''
 * }
 */
class JsonResponse implements \JsonSerializable
{
    /**
     * Data to be returned
     * @var mixed
     */
    private $data = [];

    /**
     * @var int
     */
    private $code = 0;

    /**
     * Error message in case process is not success. This will be a string.
     *
     * @var string
     */
    private $message = '';

    /**
     * @var bool
     */
    private $showData = false;

    /**
     * JsonResponse constructor.
     * @param bool    $showData
     * @param mixed   $data
     * @param int $code
     * @param string  $message
     */
    public function __construct($showData = false, $data = [], $code = 0, string $message = '')
    {
        if ($this->shouldBeJson($data)) {
            $this->data = $data;
        }

        $this->showData = $showData;
        $this->code     = $code;
        $this->message  = $message;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        if ($this->showData) {
            return [
                'code'    => $this->code,
                'message' => $this->message,
                'data'    => $this->data,
            ];
        }

        return [
            'code'    => $this->code,
            'message' => $this->message,
        ];
    }

    /**
     * Determine if the given content should be turned into JSON.
     *
     * @param  mixed  $content
     * @return bool
     */
    private function shouldBeJson($content): bool
    {
        return $content instanceof Arrayable ||
                        $content instanceof Jsonable ||
                        $content instanceof \ArrayObject ||
                        $content instanceof \JsonSerializable ||
                        is_array($content);
    }
}
