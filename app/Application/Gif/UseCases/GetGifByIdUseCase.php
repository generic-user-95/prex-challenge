<?php

declare(strict_types=1);

namespace App\Application\Gif\UseCases;

use App\Application\Gif\DTOs\GifDTO;
use App\Application\Gif\Queries\GetGifByIdQuery;
use App\Domain\Gif\Exceptions\GifNotFoundException;
use App\Domain\Gif\Ports\GiphyGatewayInterface;
use App\Domain\Gif\ValueObjects\GifId;

final class GetGifByIdUseCase
{
    public function __construct(
        private GiphyGatewayInterface $giphyGateway
    ) {
    }

    public function execute(GetGifByIdQuery $query): GifDTO
    {
        $gifId = new GifId($query->id);

        $gif = $this->giphyGateway->findById($gifId);
        if ($gif === null) {
            throw new GifNotFoundException("GIF with ID '{$gifId->value()}' not found.");
        }

        return GifDTO::fromEntity($gif);
    }
}
