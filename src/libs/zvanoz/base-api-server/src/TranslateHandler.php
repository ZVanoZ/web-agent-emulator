<?php


namespace ZVanoZ\BaseApiServer;


class TranslateHandler
    implements TranslateHandlerInterface
{
    protected string $targetLang;
    protected array $allowLangs = [];
    protected array $translates = [];

    public function __construct(
        ?string $targetLang = null
    )
    {
        if (is_string($targetLang)) {
            $this->targetLang = $targetLang;
        }
    }

    public function getTargetLang(): string
    {
        return $this->targetLang;
    }

    public function setTargetLang($value): self
    {
        if (!array_key_exists($value, $this->allowLangs)) {
            throw new Exception('Laguage not allowed');
        }
        $this->targetLang = $value;
        return $this;
    }

    function getAllowLangs(): array
    {
        return $this->allowLangs;
    }

    public function translate(
        string $value
    ): string
    {
        $result = $this->translateByArrayRef($value, $this->translates);
        return $result;
    }

    function translateByArrayRef(
        string $value,
        array  &$translatesByLang
    ): string
    {
        if(!array_key_exists($this->targetLang, $translatesByLang)){
            return $value;
        }
        $translates = $translatesByLang[$this->targetLang];
        if (!is_array($translates)) {
            return $value;
        }
        if(!array_key_exists($value, $translates)){
            return $value;
        }
        $result = $translates[$value];
        if (empty($result)) {
            return $value;
        }
        return $result;
    }

    function translateByArrayCopy(
        string $value,
        array  $translatesByLang
    ): string
    {
        $result = $this->translateByArrayRef($value, $translatesByLang);
        return $result;
    }
}