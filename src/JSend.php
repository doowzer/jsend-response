<?php

namespace JSend;

class JSend
{
    const SUCCESS = 'success';
    const ERROR = 'error';
    const FAIL = 'fail';

    private $response = [];


    /**
     * Create error response.
     * @param string $message
     * @return $this
     * @throws JSendException
     */
    public function error($message)
    {
        if (empty($message) || !is_string($message)) {
            throw new JSendException('Message is required');
        }

        $this->setStatus(self::ERROR);
        $this->setMessage($message);

        return $this;
    }

    /**
     * Create error message from exception.
     * @param $exception
     * @return $this
     * @throws JSendException
     */
    public function errorException($exception)
    {
        if (!$exception instanceof \Throwable) {
            throw new JSendException('parameter must be an instance of \Throwable');
        }

        $this->setStatus(self::ERROR);
        $this->setCode($exception->getCode());
        $this->setMessage($exception->getMessage());

        $this->setData([
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);

        return $this;
    }

    /**
     * Create fail response.
     * @param array $data
     * @return $this
     * @throws JSendException
     */
    public function fail($data = [])
    {
        $this->setStatus(self::FAIL);
        $this->setData($data);

        return $this;
    }

    /**
     * Create success response.
     * @param array $data
     * @return $this
     * @throws JSendException
     */
    public function success($data = [])
    {
        $this->setStatus(self::SUCCESS);
        $this->setData($data);

        return $this;
    }

    /**
     * @param int $code
     * @return $this
     * @throws JSendException
     */
    public function setCode($code)
    {
        $this->checkResponse();

        if ($this->getStatus() !== self::ERROR) {
            throw new JSendException('Code can only be set when an error message is created');
        }

        $this->response['code'] = $code;

        return $this;
    }

    /**
     * Set data in response.
     * @param array $data
     * @return $this
     * @throws JSendException
     */
    public function setData($data = [])
    {
        $this->checkResponse();

        $this->response['data'] = empty($data) ? null : $data;

        return $this;
    }

    /**
     * Add data to response data.
     * @param array $data
     * @return $this
     * @throws JSendException
     */
    public function addData($data = [])
    {
        if (empty($this->response['data'])) {
            return $this->setData($data);
        }

        return $this->setData(array_merge($this->response['data'], $data));
    }

    /**
     * Magic method.
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->response);
    }

    /**
     * JSON encode response.
     * @return string
     */
    public function getJson()
    {
        return json_encode($this->response);
    }

    /**
     * Get response status.
     * @return mixed
     */
    private function getStatus()
    {
        return $this->response['status'];
    }

    /**
     * Set response status.
     * @param string $status
     */
    private function setStatus($status)
    {
        $this->response['status'] = $status;
    }

    /**
     * Set response message.
     * @param $message
     */
    private function setMessage($message)
    {
        $this->response['message'] = $message;
    }

    /**
     * Check if response contains an status.
     * @throws JSendException
     */
    private function checkResponse()
    {
        // Make sure that an status is set before setting code and/or data.
        if (!array_key_exists('status', $this->response)) {
            throw new JSendException('Response must contain an status');
        }
    }
}
