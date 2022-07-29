<?php


namespace ZVanoZ\BaseApiServer\Response;

use ZVanoZ\BaseApiServer\Response;

class JsonResponse
    extends Response
{
    protected int $jsonEncodeFlags = 0;
    protected array $data = [];

    public function __construct(
        ?array $data = null,
        ?int   $httpCode = null
    )
    {
        parent::__construct();
        $this->headers->set('Content-Type', ' application/json; charset=utf-8');
        if (is_array($data)) {
            $this->setData($data);
        }
        if (!is_null($httpCode)) {
            $this->setHttpCode($httpCode);
        }
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(
        array $data
    ): self
    {
        $this->data = $data;
        return $this;
    }

    public function setItems(
        array   $items,
        ?string $pathSeparator = null
    ): self
    {
        foreach ($items as $key => &$val) {
            $this->setItem($key, $val, $pathSeparator);
        }
        return $this;
    }

    public function setItem(
        string  $nodeName,
                $nodeValue,
        ?string $pathSeparator = null
    ): self
    {
        if (is_null($pathSeparator)) {
            $this->data[$nodeName] = $nodeValue;
        } else {
            $nodes = explode($pathSeparator, $nodeName);
            $currentNodeRef = null;
            foreach ($nodes as $currentNodeName) {
                if (is_null($currentNodeRef)) {
                    $currentNodeRef = &$this->data;
                }
                $currentNodeRef[$currentNodeName] = [];
                $currentNodeRef = &$currentNodeRef[$currentNodeName];
            }
            if (is_null($currentNodeRef)) {
                $this->data[$nodeName] = $nodeValue;
            } else{
                $currentNodeRef = $nodeValue;
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function getBody(): string
    {
        $result = json_encode($this->data, $this->jsonEncodeFlags);
        return $result;
    }

    /**
     * @param int $jsonEncodeFlags
     * @return $this
     */
    public function setJsonEncodeFlags(
        int $jsonEncodeFlags
    ): self
    {
        $this->jsonEncodeFlags = $jsonEncodeFlags;
        return $this;
    }

    /**
     * @return int
     */
    public function getJsonEncodeFlags(): int
    {
        return $this->jsonEncodeFlags;
    }
}