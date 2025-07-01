<?php

namespace ViewModel;

use Hyva\OptimizedCspAllowlist\Model\Collector\ViewModelPolicyCollector;
use Hyva\OptimizedCspAllowlist\ViewModel\Hashes;
use Hyva\OptimizedCspAllowlist\ViewModel\Hosts;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ViewModelTest extends TestCase
{
    private MockObject|ViewModelPolicyCollector $policyCollector;

    protected function setUp(): void
    {
        $this->policyCollector = $this->createPartialMock(ViewModelPolicyCollector::class,
            ['addHashes', 'addHosts']
        );
    }

    public function testAddHashesIsCalledOnViewModelPolicyCollector(): void
    {
        $policyCollector = $this->policyCollector;

        $policyCollector->expects($this->once())
            ->method('addHashes')
            ->with('script-src', [
                'https://'
            ]);

        $hashes = new Hashes($policyCollector);

        $hashes->add('script-src', [
            'https://'
        ]);
    }

    public function testAddHostsIsCalledOnViewModelPolicyCollector(): void
    {
        $policyCollector = $this->policyCollector;

        $policyCollector->expects($this->once())
            ->method('addHosts')
            ->with('script-src', [
                'sha256-hyva'
            ]);

        $hashes = new Hosts($policyCollector);

        $hashes->add('script-src', [
            'sha256-hyva'
        ]);
    }



}