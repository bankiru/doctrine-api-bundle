<?php

namespace Bankiru\Api;

trigger_error(
    sprintf('Class "%s" is deprectated, use "%s" instead', ApiBundle::class, BankiruDoctrineApiBundle::class),
    E_USER_DEPRECATED
);

/**
 * @deprecated
 */
final class ApiBundle extends BankiruDoctrineApiBundle
{
}
