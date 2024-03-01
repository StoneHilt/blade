<?php

namespace StoneHilt\Blade\Tests\Unit\Directives;

use Avastechnology\Iolaus\Traits\InvokeMethod;
use Avastechnology\Iolaus\Traits\InvokeSetter;
use Illuminate\View\ComponentAttributeBag;
use PHPUnit\Framework\MockObject\Exception;
use StoneHilt\Blade\Directives\Form;
use StoneHilt\Blade\Tests\Unit\TestCase;

/**
 * Class FormTest
 *
 */
class FormTest extends TestCase
{
    use InvokeMethod;
    use InvokeSetter;

    /**
     * @dataProvider provider__invoke
     * @param array $expression
     * @param array|null $determineMethodUriFromRoute
     * @param array $renderOpeningExpects
     * @return void
     * @throws Exception
     */
    public function test__invoke(array $expression, ?array $determineMethodUriFromRoute, array $renderOpeningExpects)
    {
        $renderOpeningReturn = uniqid(microtime(true));

        $form = $this->createPartialMock(Form::class, ['determineMethodUriFromRoute', 'renderOpening']);

        if (is_array($determineMethodUriFromRoute)) {
            $form->expects($this->once())
                ->method('determineMethodUriFromRoute')
                ->willReturnMap([$determineMethodUriFromRoute]);
        } else {
            $form->expects($this->never())->method('determineMethodUriFromRoute');
        }

        $form->method('renderOpening')
            ->willReturnCallback(
                function (string $method, string $action, ComponentAttributeBag $attributes) use ($renderOpeningExpects, $renderOpeningReturn) {
                    $this->assertEquals(
                        $renderOpeningExpects['method'],
                        $method,
                        'Form::renderOpening $method mismatch'
                    );

                    $this->assertEquals(
                        $renderOpeningExpects['action'],
                        $action,
                        'Form::renderOpening $action mismatch'
                    );

                    if (array_diff_assoc($renderOpeningExpects['attributes'], $attributes->getAttributes()) !== []) {
                        $this->assertEquals(
                            $renderOpeningExpects['attributes'],
                            $attributes->getAttributes(),
                            'Form::renderOpening $attributes mismatch'
                        );
                    }

                    return $renderOpeningReturn;
                }
            );

        $this->assertEquals(
            $renderOpeningReturn,
            $form(...$expression)
        );
    }

    /**
     * @return array
     */
    public static function provider__invoke(): array
    {
        return [
            [
                'expression' => ['GET', '/page/2'],
                'determineMethodUriFromRoute' => null,
                'renderOpeningExpects' => [
                    'method' => 'GET',
                    'action' => '/page/2',
                    'attributes' => []
                ],
            ],
            [
                'expression' => [['method' => 'GET', 'action' => '/page/2']],
                'determineMethodUriFromRoute' => null,
                'renderOpeningExpects' => [
                    'method' => 'GET',
                    'action' => '/page/2',
                    'attributes' => []
                ],
            ],
            [
                'expression' => [['route' => 'login']],
                'determineMethodUriFromRoute' => ['login', ['POST', '/login']],
                'renderOpeningExpects' => [
                    'method' => 'POST',
                    'action' => '/login',
                    'attributes' => []
                ],
            ],
            [
                'expression' => [['method' => 'GET', 'action' => '/page/2', 'id' => 'The-id', 'class' => 'special-form']],
                'determineMethodUriFromRoute' => null,
                'renderOpeningExpects' => [
                    'method' => 'GET',
                    'action' => '/page/2',
                    'attributes' => ['id' => 'The-id', 'class' => 'special-form']
                ],
            ],
        ];
    }

    /**
     * @dataProvider provider_renderOpening
     * @param string $method
     * @param string $action
     * @param array $attributes
     * @param string $csrfInput
     * @param string $expected
     * @return void
     * @throws Exception
     * @throws \ReflectionException
     */
    public function test_renderOpening(string $method, string $action, array $attributes, string $csrfInput, string $expected)
    {
        $form = $this->createPartialMock(Form::class, ['csrfInput']);
        $form->method('csrfInput')->willReturn($csrfInput);

        $this->assertEquals(
            $expected,
            $this->invokeMethod($form, 'renderOpening', [$method, $action, new ComponentAttributeBag($attributes)])
        );
    }

    /**
     * @return array[]
     */
    public static function provider_renderOpening(): array
    {
        return [
            [
                'method'     => 'GET',
                'action'     => '/page/1',
                'attributes' => [],
                'csrfInput'  => '<input name="_csrf" value="a1b2c3d4e5f6" />',
                'expected'   => '<form method="GET" action="/page/1">',
            ],
            [
                'method'     => 'POST',
                'action'     => '/page/2',
                'attributes' => [],
                'csrfInput'  => '<input name="_csrf" value="a1b2c3d4e5f6" />',
                'expected'   => '<form method="POST" action="/page/2"><input name="_csrf" value="a1b2c3d4e5f6" />',
            ],
            [
                'method'     => 'PUT',
                'action'     => '/page/3',
                'attributes' => [],
                'csrfInput'  => '<input name="_csrf" value="a1b2c3d4e5f6" />',
                'expected'   => '<form method="POST" action="/page/3"><input name="_method" value="PUT" /><input name="_csrf" value="a1b2c3d4e5f6" />',
            ],
            [
                'method'     => 'PATCH',
                'action'     => '/page/4',
                'attributes' => [],
                'csrfInput'  => '<input name="_csrf" value="a1b2c3d4e5f6" />',
                'expected'   => '<form method="POST" action="/page/4"><input name="_method" value="PATCH" /><input name="_csrf" value="a1b2c3d4e5f6" />',
            ],
            [
                'method'     => 'DELETE',
                'action'     => '/page/5',
                'attributes' => [],
                'csrfInput'  => '<input name="_csrf" value="a1b2c3d4e5f6" />',
                'expected'   => '<form method="POST" action="/page/5"><input name="_method" value="DELETE" /><input name="_csrf" value="a1b2c3d4e5f6" />',
            ],
            [
                'method'     => 'POST',
                'action'     => '/page/2',
                'attributes' => ['id' => 'the-id'],
                'csrfInput'  => '<input name="_csrf" value="a1b2c3d4e5f6" />',
                'expected'   => '<form id="the-id" method="POST" action="/page/2"><input name="_csrf" value="a1b2c3d4e5f6" />',
            ],
        ];
    }
}
