<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @author    Sven Hagemann <sven@rednose.nl>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Reflection\DocBlock\Type\Collection;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock;

use Mockery as m;

/**
 * Test class for \phpDocumentor\Descriptor\Builder
 *
 * @covers \phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler
 */
class FunctionAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /** @var FunctionAssembler $fixture */
    protected $fixture;

    /** @var ArgumentAssembler|m\MockInterface */
    protected $argumentAssemblerMock;

    /** @var ProjectDescriptorBuilder|m\MockInterface */
    protected $builderMock;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp()
    {
        $this->builderMock = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $this->argumentAssemblerMock = m::mock('phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler');

        $this->fixture = new FunctionAssembler($this->argumentAssemblerMock);
        $this->fixture->setBuilder($this->builderMock);
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\Reflector\FunctionAssembler::create
     */
    public function testCreateFunctionDescriptorFromReflector()
    {
        // Arrange
        $namespace    = 'Namespace';
        $functionName = 'goodbyeWorld';
        $argumentName = 'waveHand';

        $argumentDescriptorMock = $this->givenAnArgumentWithName($argumentName);
        $functionReflectorMock = $this->givenAFunctionReflector(
            $namespace,
            $functionName,
            $argumentDescriptorMock,
            $this->givenADocBlockObject()
        );

        // Act
        $descriptor = $this->fixture->create($functionReflectorMock);

        // Assert
        $expectedFqsen = $namespace . '\\' . $functionName . '()';
        $this->assertSame($expectedFqsen, $descriptor->getFullyQualifiedStructuralElementName());
        $this->assertSame($functionName, $descriptor->getName());

        $argument = $descriptor->getArguments()->get($argumentName);
        $this->assertSame($argument, $argumentDescriptorMock);
    }

    /**
     * @param $namespace
     * @param $functionName
     * @param $argumentMock
     * @param $docBlockMock
     * @return m\MockInterface
     */
    protected function givenAFunctionReflector($namespace, $functionName, $argumentMock, $docBlockMock)
    {
        $functionReflectorMock = m::mock('phpDocumentor\Reflection\FunctionReflector');
        $functionReflectorMock->shouldReceive('getName')->andReturn($namespace . '\\' . $functionName);
        $functionReflectorMock->shouldReceive('getShortName')->andReturn($functionName);
        $functionReflectorMock->shouldReceive('getNamespace')->andReturn($namespace);
        $functionReflectorMock->shouldReceive('getDocBlock')->andReturn($docBlockMock);
        $functionReflectorMock->shouldReceive('getLinenumber')->andReturn(128);
        $functionReflectorMock->shouldReceive('getArguments')->andReturn(array($argumentMock));

        return $functionReflectorMock;
    }

    /**
     * @return m\MockInterface
     */
    protected function givenADocBlockObject()
    {
        $docBlockDescription = new DocBlock\Description('This is an example description');
        $docBlockMock = m::mock('phpDocumentor\Reflection\DocBlock');
        $docBlockMock->shouldReceive('getTagsByName')->andReturn(array());
        $docBlockMock->shouldReceive('getTags')->andReturn(array());
        $docBlockMock->shouldReceive('getShortDescription')->andReturn('This is a example description');
        $docBlockMock->shouldReceive('getLongDescription')->andReturn($docBlockDescription);

        return $docBlockMock;
    }

    /**
     * Prepares a mock Argument with the given name.
     *
     * @param string $argumentName
     *
     * @return ArgumentDescriptor|m\MockInterface
     */
    protected function givenAnArgumentWithName($argumentName)
    {
        $argumentMock = m::mock('phpDocumentor\Descriptor\ArgumentDescriptor');
        $argumentMock->shouldReceive('getName')->andReturn($argumentName);

        $this->argumentAssemblerMock->shouldReceive('create')->andReturn($argumentMock);

        return $argumentMock;
    }
}
