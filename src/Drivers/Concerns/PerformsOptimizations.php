<?php

namespace Spatie\Image\Drivers\Concerns;

use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\OptimizerChainFactory;

trait PerformsOptimizations
{
    protected bool $optimize = false;

    protected OptimizerChain $optimizerChain;

    public function optimize(?OptimizerChain $optimizerChain = null): static
    {
        $this->optimize = true;
        $this->optimizerChain = $optimizerChain ?? OptimizerChainFactory::create();

        return $this;
    }
}
