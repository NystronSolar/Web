<?php

namespace App\Utils\Chart;

/**
 * This class represent an Chart Dataset. It's not recommended to be used lonely. Use \App\Utils\Chart\Chart to master the power of charts! Inspired by Chart.js.
 *
 * @see \App\Utils\Chart\Chart Chart Class
 * @see https://www.chartjs.org/ Chart JS Oficial Docs
 */
class ChartDataset
{
    private ?string $backgroundColor = null;

    private ?string $borderColor = null;

    private ?string $key = null;

    private ?string $label = null;

    private array $data = [];

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(mixed $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function addData(mixed $data)
    {
        array_push($this->data, $data);
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(string $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    public function getBorderColor(): ?string
    {
        return $this->borderColor;
    }

    public function setBorderColor(string $borderColor): self
    {
        $this->borderColor = $borderColor;

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }
}
