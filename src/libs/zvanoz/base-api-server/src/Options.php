<?php


namespace ZVanoZ\BaseApiServer;

class Options
{
    protected ?RouterInterface $router = null;

    /**
     * @param RouterInterface|null $router
     * @return Options
     */
    public function setRouter(?RouterInterface $router): Options
    {
        $this->router = $router;
        return $this;
    }

    /**
     * @return RouterInterface|null
     */
    public function getRouter(): ?RouterInterface
    {
        return $this->router;
    }
}