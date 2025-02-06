<?php

namespace App\Service\Stream;

use App\Entity\User;
use DateTimeImmutable;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class StreamCookieGenerator
{
    public function generate(User $user): string
    {
        $tokenBuilder = new Builder(new JoseEncoder(), ChainedFormatter::default());
        $algorithm    = new Sha256();
        $signingKey   = InMemory::plainText("PEeiVd83X2NAAksbJl40Cmfg9IP8eUpP");

        $token = $tokenBuilder
            ->issuedBy("http://example.com")
            ->issuedAt(new DateTimeImmutable())
            ->withClaim("mercure", [
                "subscribe" => ["user-{$user->getUlid()}"],
            ])
            ->getToken($algorithm, $signingKey);

        return sprintf(
            "mercureAuthorization=%s; Path=/; HttpOnly;",
            $token->toString()
        );
    }
}
