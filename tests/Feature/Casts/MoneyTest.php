<?php

namespace Tests\Feature\Casts;

use App\Album;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Money\Money;
use Tests\TestCase;

class MoneyTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        config(['ihd.currency' => 'EUR']);
    }

    /** @test */
    public function testCanSet()
    {
        $album = new Album;

        $album->full_album_price = 999;

        $this->assertInstanceOf(Money::class, $album->full_album_price);
        $this->assertEquals(999, $album->full_album_price->getAmount());
        $this->assertEquals('EUR', $album->full_album_price->getCurrency()->getCode());
    }

    /** @test */
    public function testCanSetMoney()
    {
        $album = new Album;

        $album->full_album_price = Money::EUR(42);

        $this->assertInstanceOf(Money::class, $album->full_album_price);
        $this->assertEquals(42, $album->full_album_price->getAmount());
        $this->assertEquals('EUR', $album->full_album_price->getCurrency()->getCode());
    }

    /** @test */
    public function testCanSetNull()
    {
        $album = new Album;

        $album->full_album_price = null;

        $this->assertNull($album->full_album_price);
    }

    /** @test */
    public function testCanStore()
    {
        $album = factory(Album::class)->make(['full_album_price' => 999]);

        $price = $album->full_album_price;

        $this->assertInstanceOf(Money::class, $price);
        $this->assertEquals('EUR', $price->getCurrency()->getCode());

        $album->save();

        $album->refresh();

        $this->assertInstanceOf(Money::class, $album->full_album_price);
        // Immutable
        $this->assertFalse($price === $album->full_album_price);
        // But same
        $this->assertEquals($price, $album->full_album_price);
    }

    /** @test */
    public function testCanModify()
    {
        $album = new Album;

        $album->full_album_price = 999;

        /** @var Money */
        $money = $album->full_album_price;

        $album->full_album_price = $money->add(Money::EUR(600))
            ->subtract(Money::EUR(100))
            ->multiply(2)
            ->divide(2);

        $this->assertEquals(1499, $album->full_album_price->getAmount());
    }
}
