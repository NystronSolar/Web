<?php

namespace App\Utils\Chart;

/**
 * This class represent an Chart. It can be used in the Front End, to build charts. Inspired by Chart.js.
 *
 * @see https://www.chartjs.org/ Chart JS Oficial Docs
 */
class Chart
{
    private ?Type $type = null;

    /** @var string[] */
    private array $labels = [];

    /** @var array<string, ChartDataset> */
    private array $datasets = [];

    /** @return string[] */
    public function getLabels(): array
    {
        return $this->labels;
    }

    public function setLabels(array $labels): self
    {
        $this->labels = $labels;

        return $this;
    }

    public function addLabel(string $label): void
    {
        array_push($this->labels, $label);
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDatasets(): array
    {
        return $this->datasets;
    }

    public function setDatasets(array $datasets): self
    {
        $this->datasets = $datasets;

        return $this;
    }

    public function addDataset(ChartDataset $dataset): void
    {
        array_push($this->datasets, $dataset);
    }
}
