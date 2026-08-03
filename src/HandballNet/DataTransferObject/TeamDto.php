<?php

declare(strict_types=1);

namespace Janborg\H4aTabellen\HandballNet\DataTransferObject;

use Janborg\H4aTabellen\HandballNet\Enum\AgeGroup;
use Janborg\H4aTabellen\HandballNet\Enum\Provider;
use Janborg\H4aTabellen\HandballNet\Enum\Verband;

final readonly class TeamDto
{
    public function __construct(
        // Team
        public string $id,
        public string $name,
        public string $acronym,
        public string|null $logo,
        public string $teamGroupId,
        public string|null $livestreamUrl,
        // ID-Parts (aus explode('.', $id))
        public Provider $provider,
        public Verband $verband,
        public string $teamLocalId,
        // z.B. "1325861" Tournament
        public string $tournamentId,
        public string $tournamentName,
        public string $tournamentAcronym,
        public string $tournamentType,
        public AgeGroup|null $ageGroup,
    ) {
    }

    /**
     * Build team DataTransferObject from data coming from handball.net API.
     *
     * @param array<mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $idParts = explode('.', $data['id']);

        if (\count($idParts) < 3) {
            throw new \InvalidArgumentException(\sprintf('Ungültiges ID-Format: "%s". Erwartet: "provider.verband.id"', $data['id']));
        }

        $provider = Provider::tryFrom($idParts[0])
            ?? throw new \InvalidArgumentException(\sprintf('Unbekannter Provider: "%s"', $idParts[0]));

        $verband = Verband::tryFrom($idParts[1])
            ?? throw new \InvalidArgumentException(\sprintf('Unbekannter Verband: "%s"', $idParts[1]));

        $tournament = $data['defaultTournament'] ?? [];

        return new self(
            // Team
            id: $data['id'],
            name: $data['name'],
            acronym: $data['acronym'] ?? '',
            logo: $data['logo'] ?? null,
            teamGroupId: (string) $data['teamGroupId'],
            livestreamUrl: $data['livestreamUrl'] ?? null,

            // ID-Parts
            provider: $provider,
            verband: $verband,
            teamLocalId: $idParts[2],

            // Tournament
            tournamentId: $tournament['id'] ?? '',
            tournamentName: $tournament['name'] ?? '',
            tournamentAcronym: $tournament['acronym'] ?? '',
            tournamentType: $tournament['tournamentType'] ?? '',
            ageGroup: AgeGroup::tryFrom($tournament['ageGroup'] ?? ''),
        );
    }
}
