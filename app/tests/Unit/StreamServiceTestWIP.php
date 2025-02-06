<?php

namespace App\Tests\Unit;

use App\Entity\Order;
use App\Entity\Wave;
use App\Entity\Shop;
use App\Service\StreamService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Twig\Environment;

class StreamServiceTestWIP extends TestCase
{
    // private HubInterface&MockObject $hubInterfaceMock;
    // private Environment&MockObject $twigMock;
    // private FormFactoryInterface&MockObject $formFactoryInterfaceMock;
    // private StreamService $streamService;

    // protected function setUp(): void
    // {
    //     $this->hubInterfaceMock = $this->createMock(HubInterface::class);
    //     $this->twigMock = $this->createMock(Environment::class);
    //     $this->formFactoryInterfaceMock = $this->createMock(FormFactoryInterface::class);
    //     $this->streamService = new StreamService(
    //         $this->hubInterfaceMock,
    //         $this->twigMock,
    //         $this->formFactoryInterfaceMock
    //     );
    // }

    // /**
    //  * @group unit
    //  */
    // public function testPublishOnSpecificWaveTopics(): void
    // {
    //     /** @var Order&MockObject $order */
    //     $order = $this->createMock(Order::class);
    //     /** @var Wave&MockObject $wave */
    //     $wave = $this->createMock(Wave::class);

    //     $waveUlid = "123456789";
    //     $wave->method("getUlid")->willReturn($waveUlid);
    //     $order->method("getWave")->willReturn($wave);

    //     $this->hubInterfaceMock->expects($this->once())
    //         ->method("publish")
    //         ->with(
    //             $this->callback(function ($update) use ($waveUlid) {
    //                 return $this->updateHasValidTopicAndData("wave-$waveUlid", [""], $update);
    //             })
    //         );

    //     $this->streamService->publishOnSpecificWaveTopic($wave->getUlid(), [""]);
    // }

    // /**
    //  * @group unit
    //  */
    // public function testPublishOnSpecificShopTopics(): void
    // {
    //     /** @var Shop&MockObject $shop */
    //     $shop = $this->createMock(Shop::class);

    //     $shopUlid = "123456789";
    //     $shop->method("getUlid")->willReturn($shopUlid);

    //     $this->hubInterfaceMock->expects($this->once())
    //         ->method("publish")
    //         ->with(
    //             $this->callback(function ($update) use ($shopUlid) {
    //                 return $this->updateHasValidTopicAndData("shop-$shopUlid", [""], $update);
    //             })
    //         );

    //     $this->streamService->publishOnSpecificShopTopic($shop->getUlid(), [""]);
    // }

    // /**
    //  * @group unit
    //  */
    // public function testPublishNewOrderHandlesInvalidOrderData(): void
    // {
    //     /** @var Order&MockObject $order */
    //     $order = $this->createMock(Order::class);
    //     /** @var Shop&MockObject $shop */
    //     $shop = $this->createMock(Shop::class);

    //     $order->method("getWave")->willReturn(null);

    //     $this->expectException(InvalidArgumentException::class);
    //     $this->expectExceptionMessage("The ulid field of an entity is not valid for publishing.");

    //     $this->streamService->publishNewOrder($order, $shop);
    // }

    // /**
    //  * @group unit
    //  */
    // public function testPublishNewOrderPublishesOnAppropriateTopics(): void
    // {
    //     /** @var Order&MockObject $order */
    //     $order = $this->createMock(Order::class);
    //     /** @var Wave&MockObject $wave */
    //     $wave = $this->createMock(Wave::class);
    //     /** @var Shop&MockObject $shop */
    //     $shop = $this->createMock(Shop::class);

    //     $orderUlid = "123456789";
    //     $wave->method("getUlid")->willReturn($orderUlid);
    //     $order->method("getWave")->willReturn($wave);

    //     $shopUlid = "987654321";
    //     $shop->method("getUlid")->willReturn($shopUlid);

    //     $renderedTableRowOrder = "<tr>Order row</tr>";
    //     $renderedFlashMessage = "<p>Flash message</p>";
    //     $renderedTableRowWave = "<tr>Order wave</tr>";

    //     $this->twigMock->expects($this->exactly(3))
    //         ->method("render")
    //         ->willReturnMap([
    //             [
    //                 'components/atoms/table_row_order/table_row_order.stream.html.twig',
    //                 [
    //                     "stream_action" => "append",
    //                     "stream_target" => "stream_table_row_order",
    //                     "order" => $order,
    //                 ],
    //                 $renderedTableRowOrder
    //             ],
    //             [
    //                 'components/organisms/flash_msg/flash_msg.stream.html.twig',
    //                 [
    //                     "stream_action" => "append",
    //                     "stream_target" => "stream_flash_msg",
    //                     "stream_flashes" => ["success" => ["Nouvelle commande."]],
    //                 ],
    //                 $renderedFlashMessage
    //             ],
    //             [
    //                 'components/atoms/table_row_wave/table_row_wave.stream.html.twig',
    //                 [
    //                     "stream_action" => "replace",
    //                     "stream_target" => "stream_table_row_wave_{$wave->getUlid()}",
    //                     "shop" => $shop,
    //                     "wave" => $wave,
    //                 ],
    //                 $renderedTableRowWave
    //             ],
    //         ]);

    //     $this->hubInterfaceMock->expects($this->exactly(2))
    //         ->method("publish")
    //         ->withConsecutive(
    //             [
    //                 $this->callback(
    //                     function ($update) use ($orderUlid, $renderedTableRowOrder, $renderedTableRowWave) {
    //                         return $this->updateHasValidTopicAndData(
    //                             "wave-$orderUlid",
    //                             [
    //                                 $renderedTableRowOrder,
    //                                 $renderedTableRowWave
    //                             ],
    //                             $update
    //                         );
    //                     }
    //                 )
    //             ],
    //             [
    //                 $this->callback(function ($update) use ($shopUlid, $renderedFlashMessage) {
    //                     return $this->updateHasValidTopicAndData(
    //                         "shop-$shopUlid",
    //                         [$renderedFlashMessage],
    //                         $update
    //                     );
    //                 })
    //             ]
    //         );

    //     $this->streamService->publishNewOrder($order, $shop);
    // }

    // /**
    //  * @param array<int, string> $expectedData
    //  */
    // private function updateHasValidTopicAndData(string $expectedTopic, array $expectedData, Update $update): bool
    // {
    //     $actualData = json_decode($update->getData(), true);
    //     return $expectedTopic === $update->getTopics()[0] && $expectedData === $actualData;
    // }
}
