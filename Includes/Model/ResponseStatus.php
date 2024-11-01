<?php

declare(strict_types=1);

namespace UpsTracking\Includes\Model;

final class ResponseStatus
{
    private string $code;
    private string $description;

    private function __construct(string $code, string $description)
    {
        $this->code = $code;
        $this->description = $description;
    }

    public static function fromResponse(array $responseStatus): ResponseStatus {
        $code = $responseStatus['Code'];
        $description = $responseStatus['Description'];

        return ResponseStatus::from($code, $description);
    }

    public static function fromFault(array $fault): ResponseStatus {

        $primaryErrorCode = $fault['detail']['Errors']['ErrorDetail']['PrimaryErrorCode'];

        $code = $primaryErrorCode['Code'];
        $description = $primaryErrorCode['Description'];

        return ResponseStatus::from($code, $description);
    }

    public static function from(string $code, string $description): ResponseStatus {
        return new ResponseStatus($code, $description);
    }

    /**
     * @return mixed|string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed|string
     */
    public function getDescription()
    {
        return $this->description;
    }
}