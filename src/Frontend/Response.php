<?php

namespace Application\Frontend;

class Response
{
    /**
     * @var array
     */
    private $header = [];

    /**
     * @var int
     */
    private $statusCode = 200;

    /**
     * @var string
     */
    private $content;

    /**
     * @param string $header
     * @return Response
     */
    public function addHeader(string $header): self
    {
        $this->header[] = $header;
        return $this;
    }

    /**
     * @return Response
     */
    public function clearHeaders(): self
    {
        $this->header = [];
        return $this;
    }

    /**
     * @param string $content
     * @return Response
     */
    public function setContent(string $content): Response
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param int $statusCode
     * @return Response
     */
    public function setStatusCode(int $statusCode): Response
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @param string $location
     * @param int|null $statusCode
     */
    public function redirect(string $location, ?int $statusCode = 302)
    {
        http_response_code($statusCode);
        header("Location: $location");
        die();
    }

    public function send()
    {
        http_response_code($this->statusCode);
        foreach ($this->header as $header) {
            header($header);
        }

        echo $this->content;
    }
}