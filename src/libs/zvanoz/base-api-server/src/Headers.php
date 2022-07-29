<?php


namespace ZVanoZ\BaseApiServer;

class Headers
{
    protected array $list = [];

    /**
     * @return array
     */
    public function getListCopy(): array
    {
        return $this->list;
    }
    public function &getListRef(): array
    {
        return $this->list;
    }
    protected function getNormalizedName(string $name)
    {
        $result = strtolower($name);
        return $result;
    }

    public function get(string $name, $valueNotFound = null)
    {
        $name = $this->getNormalizedName($name);
        if (array_key_exists($name, $this->list)) {
            return $this->list[$name];
        }
        return $valueNotFound;
    }
    public function setFromHeaders(Headers $headers): self
    {
        $this->setFromArray($headers->getListRef());
        return $this;
    }
    public function setFromArray(array $headers): self
    {
        foreach ($headers as $headerName => $headerValue){
            $this->set($headerName, $headerValue);
        }
        return $this;
    }
    public function set(string $name, $value): self
    {
        $name = $this->getNormalizedName($name);
        $this->list[$name] = $value;
        return $this;
    }

    public function remove(string $name): self
    {
        $name = $this->getNormalizedName($name);
        if (array_key_exists($name, $this->list)) {
            unset($this->list[$name]);
        }
        return $this;
    }

    public function appendFromRawRequest(): self
    {
        $headers = getallheaders();
        foreach ($headers as $headerName => $headerValue) {
            $this->set($headerName, $headerValue);
        }
        return $this;
    }


}
