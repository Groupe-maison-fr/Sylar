<?php

declare(strict_types=1);

namespace Micoli\Trail\tests;

use Micoli\Trail\tests\Fixtures\Bar;
use Micoli\Trail\tests\Fixtures\Foo;
use Micoli\Trail\Trail;
use Micoli\Trail\TrailException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class TrailTest extends TestCase
{
    /** @var Foo[] */
    private array $foos;
    private Foo $foo;
    private array $complexArray;

    protected function setUp(): void
    {
        parent::setUp();
        $this->foos = [
            new Foo(
                1,
                'a',
                [
                    new Bar(11, 'aa', '_aa', '_zz'),
                    new Bar(22, 'bb', '_aaa', '_zzz'),
                    new Bar(33, 'cc', '_aaaa', '_zzzz'),
                ],
            ),
            new Foo(
                2,
                'b',
                [
                ],
            ),
            new Foo(
                3,
                'c',
                [
                    new Bar(111, '_aa', '__aa', '__zz'),
                    new Bar(222, '_bb', '__aaa', '__zzz'),
                    new Bar(333, '_cc', '__aaaa', '__zzzz'),
                ],
            ),
        ];
        $this->foo = new Foo(1, 'a', [
            new Bar(11, 'aa', '_a', '_z'),
            new Bar(22, 'bb', '_ba', '_bz'),
            new Bar(33, 'cc', '_ca', '_cz'),
        ]);
        $this->complexArray = [
            'a' => ['b' => ['c' => [1, 2, [['d' => 44], 22, 33], 3]]],
        ];
    }

    /**
     * @test
     */
    public function it_should_raise_exception_if_pattern_is_malformed(): void
    {
        self::expectException(TrailException::class);
        self::expectExceptionMessage('Could not parse property path "[a][b]a". Unexpected token "a" at position 6.');
        Trail::create($this->complexArray)->path('[a][b]a')->get();
    }

    /**
     * @test
     */
    public function it_should_raise_exception_if_pattern_is_invalid(): void
    {
        self::expectException(TrailException::class);
        self::expectExceptionMessage('Can not execute "count" on non iterable value "1"');
        Trail::create($this->complexArray)->path('[a][b][c]|@first|@count')->get();
    }

    /**
     * @test
     */
    public function it_should_get_a_value_in_a_complex_array(): void
    {
        self::assertSame(3, Trail::create($this->complexArray)->path('[a][b][c]|@last')->get());
        self::assertSame(1, Trail::create($this->complexArray)->path('[a][b][c]|@first')->get());
        self::assertSame(3, Trail::create($this->complexArray)->path('[a]')->path('[b][c]|@last')->get());
        self::assertSame(1, Trail::create($this->complexArray)->path('[a]')->path('[b][c]|@first')->get());

        self::assertSame(3, Trail::eval($this->complexArray, '[a][b][c]|@last'));
        self::assertSame(1, Trail::eval($this->complexArray, '[a][b][c]|@first'));
        self::assertSame(44, Trail::eval($this->complexArray, '[a][b][c][2]|@first|[d]'));
        self::assertSame(44, Trail::eval($this->complexArray, ['[a][b][c][2]', '@first', '[d]']));
        self::assertSame(44, Trail::eval($this->complexArray, ['[a][b][c]', '[2]', '@first', '[d]']));
        self::assertSame(3, Trail::eval($this->complexArray, ['[a][b][c]', '[2]', '@count']));
    }

    /**
     * @test
     */
    public function it_should_get_a_value_in_a_complex_object(): void
    {
        self::assertSame(1, Trail::create($this->foos)->path('@first|count')->get());
        self::assertSame(3, Trail::create($this->foos)->path('@last|bars|@count')->get());
        self::assertSame(3, Trail::create($this->foos)->path('@count')->get());
        self::assertSame(1, Trail::create($this->foos)->path('[0]|count')->get());
        self::assertSame(1, Trail::create($this->foos)->path('[0]|count')->get());
        self::assertSame(1, Trail::create($this->foos)->path(['[0]', 'count'])->get());
        self::assertSame(1, Trail::eval($this->foos, ['[0]', 'count']));
        self::assertSame('_aa', Trail::eval($this->foos, '@first|bars|@first|first'));
    }

    /**
     * @test
     */
    public function it_should_get_a_value_in_a_simple_object(): void
    {
        self::assertSame(1, Trail::create($this->foo)->path('count')->get());
        self::assertSame(11, Trail::create($this->foo)->path('bars[0].number')->get());
        self::assertSame(11, Trail::create($this->foo)->path('bars|@first|number')->get());
        self::assertSame(22, Trail::create($this->foo)->path('bars[1].number')->get());
        self::assertSame(33, Trail::create($this->foo)->path('bars[2].number')->get());
        self::assertSame(33, Trail::create($this->foo)->path('bars|@last|number')->get());
    }
}
