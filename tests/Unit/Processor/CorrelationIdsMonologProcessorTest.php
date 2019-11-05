<?php declare(strict_types=1);
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 */
namespace OAT\Library\CorrelationIdsMonolog\Tests\Unit\Processor;

use OAT\Library\CorrelationIds\Provider\CorrelationIdsHeaderNamesProviderInterface;
use OAT\Library\CorrelationIds\Registry\CorrelationIdsRegistryInterface;
use OAT\Library\CorrelationIdsMonolog\Processor\CorrelationIdsMonologProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CorrelationIdsMonologProcessorTest extends TestCase
{
    /** @var CorrelationIdsRegistryInterface|MockObject */
    private $registryMock;

    /** @var CorrelationIdsHeaderNamesProviderInterface|MockObject */
    private $providerMock;

    /** @var CorrelationIdsMonologProcessor */
    private $subject;

    protected function setUp(): void
    {
        $this->registryMock = $this->createMock(CorrelationIdsRegistryInterface::class);
        $this->providerMock = $this->createMock(CorrelationIdsHeaderNamesProviderInterface::class);

        $this->registryMock
            ->expects($this->once())
            ->method('getCurrentCorrelationId')
            ->willReturn('current');

        $this->registryMock
            ->expects($this->once())
            ->method('getParentCorrelationId')
            ->willReturn('parent');

        $this->registryMock
            ->expects($this->once())
            ->method('getRootCorrelationId')
            ->willReturn('root');

        $this->providerMock
            ->expects($this->once())
            ->method('provideCurrentCorrelationIdHeaderName')
            ->willReturn(CorrelationIdsHeaderNamesProviderInterface::DEFAULT_CURRENT_CORRELATION_ID_HEADER_NAME);

        $this->providerMock
            ->expects($this->once())
            ->method('provideParentCorrelationIdHeaderName')
            ->willReturn(CorrelationIdsHeaderNamesProviderInterface::DEFAULT_PARENT_CORRELATION_ID_HEADER_NAME);

        $this->providerMock
            ->expects($this->once())
            ->method('provideRootCorrelationIdHeaderName')
            ->willReturn(CorrelationIdsHeaderNamesProviderInterface::DEFAULT_ROOT_CORRELATION_ID_HEADER_NAME);

        $this->subject = new CorrelationIdsMonologProcessor($this->registryMock, $this->providerMock);
    }

    public function testItCanAddCorrelationIdsLogEntriesToAnEmptyContext(): void
    {
        $record = $this->subject->__invoke([]);

        $this->assertSame(
            [
                'extra' => [
                    CorrelationIdsHeaderNamesProviderInterface::DEFAULT_CURRENT_CORRELATION_ID_HEADER_NAME => 'current',
                    CorrelationIdsHeaderNamesProviderInterface::DEFAULT_PARENT_CORRELATION_ID_HEADER_NAME => 'parent',
                    CorrelationIdsHeaderNamesProviderInterface::DEFAULT_ROOT_CORRELATION_ID_HEADER_NAME => 'root',
                ]

            ],
            $record
        );
    }

    public function testItCanMergeCorrelationIdsLogEntriesToAnExistingContext(): void
    {
        $record = $this->subject->__invoke([
            'extra' => ['some' => 'data']
        ]);

        $this->assertSame(
            [
                'extra' => [
                    'some' => 'data',
                    CorrelationIdsHeaderNamesProviderInterface::DEFAULT_CURRENT_CORRELATION_ID_HEADER_NAME => 'current',
                    CorrelationIdsHeaderNamesProviderInterface::DEFAULT_PARENT_CORRELATION_ID_HEADER_NAME => 'parent',
                    CorrelationIdsHeaderNamesProviderInterface::DEFAULT_ROOT_CORRELATION_ID_HEADER_NAME => 'root',
                ]

            ],
            $record
        );
    }
}
