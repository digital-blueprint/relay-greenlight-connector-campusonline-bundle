<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Dbp\Relay\GreenlightConnectorCampusonlineBundle\Controller\LoggedInOnly;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={
 *         "get" = {
 *             "path" = "/greenlight-connector-campusonline/tests",
 *             "openapi_context" = {
 *                 "tags" = {"Test"},
 *             },
 *         }
 *     },
 *     itemOperations={
 *         "get" = {
 *             "path" = "/greenlight-connector-campusonline/tests/{identifier}",
 *             "openapi_context" = {
 *                 "tags" = {"Test"},
 *             },
 *         },
 *         "put" = {
 *             "path" = "/greenlight-connector-campusonline/tests/{identifier}",
 *             "openapi_context" = {
 *                 "tags" = {"Test"},
 *             },
 *         },
 *         "delete" = {
 *             "path" = "/greenlight-connector-campusonline/tests/{identifier}",
 *             "openapi_context" = {
 *                 "tags" = {"Test"},
 *             },
 *         },
 *         "loggedin_only" = {
 *             "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *             "method" = "GET",
 *             "path" = "/greenlight-connector-campusonline/tests/{identifier}/loggedin-only",
 *             "controller" = LoggedInOnly::class,
 *             "openapi_context" = {
 *                 "summary" = "Only works when logged in.",
 *                 "tags" = {"Test"},
 *             },
 *         }
 *     },
 *     iri="https://schema.org/Test",
 *     shortName="GreenlightConnectorCampusonlineTest",
 *     normalizationContext={
 *         "groups" = {"GreenlightConnectorCampusonlineTest:output"},
 *         "jsonld_embed_context" = true
 *     },
 *     denormalizationContext={
 *         "groups" = {"GreenlightConnectorCampusonlineTest:input"},
 *         "jsonld_embed_context" = true
 *     }
 * )
 */
class Test
{
    /**
     * @ApiProperty(identifier=true)
     */
    private $identifier;

    /**
     * @ApiProperty(iri="https://schema.org/name")
     * @Groups({"GreenlightConnectorCampusonlineTest:output", "GreenlightConnectorCampusonlineTest:input"})
     *
     * @var string
     */
    private $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }
}
