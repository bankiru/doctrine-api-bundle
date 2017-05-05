[![Latest Stable Version](https://poser.pugx.org/bankiru/doctrine-api-bundle/v/stable)](https://packagist.org/packages/bankiru/doctrine-api-bundle) 
[![Total Downloads](https://poser.pugx.org/bankiru/doctrine-api-bundle/downloads)](https://packagist.org/packages/bankiru/doctrine-api-bundle) 
[![Latest Unstable Version](https://poser.pugx.org/bankiru/doctrine-api-bundle/v/unstable)](https://packagist.org/packages/bankiru/doctrine-api-bundle) 
[![License](https://poser.pugx.org/bankiru/doctrine-api-bundle/license)](https://packagist.org/packages/bankiru/doctrine-api-bundle)

[![Build Status](https://travis-ci.org/bankiru/doctrine-api-bundle.svg)](https://travis-ci.org/bankiru/doctrine-api-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bankiru/doctrine-api-bundle/badges/quality-score.png)](https://scrutinizer-ci.com/g/bankiru/doctrine-api-bundle/)
[![Code Coverage](https://scrutinizer-ci.com/g/bankiru/doctrine-api-bundle/badges/coverage.png)](https://scrutinizer-ci.com/g/bankiru/doctrine-api-bundle/)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/a168559a-dd39-47ce-97ae-ecd4451ebd2d/mini.png)](https://insight.sensiolabs.com/projects/a168559a-dd39-47ce-97ae-ecd4451ebd2d)

# Doctrine-faced RPC Bundle

This bundle incorporates doctrine API library `bankiru/doctrine-api-client`

## Services

This bundle provides the only public service to use - `@bankiru_api.entity_manager`.
This service implements `ApiEntityManager extends ObjectManager` interface

## Features

### Automatic bundle metadata registration

This bundle automatically registers all bundles `yaml` annotations if they are stored at
`@BundleName\Resources\config\api` location.

### Client aggregation

This bundle automatically registers all services, marked with `rpc_client` 
tag into the library client registry, i.e

```yaml
  bankiru_api.test.client:
    class: Bankiru\Api\Tests\TestClient
    arguments:
    - "@bankiru_api.test.client_mock"
    tags:
    - { name: rpc_client, client_name: test_client }
```

### Profiling

This bundle enables RPC client profiling and time tracing with internal
Symfony components - stopwatch and web debug toolbar.

@Todo: populate with screenshots


### Cache configuration

```yml
# TTL only
api_client:
    cache:
        configuration:
            Bankiru\Api\Doctrine\Test\Entity\TestEntity: 900
```

```yml
# Default
api_client:
    cache:
        configuration:
            Bankiru\Api\Doctrine\Test\Entity\TestEntity:
                enabled: false
```

```yml
# Enabled defaults
api_client:
    cache:
        configuration:
            Bankiru\Api\Doctrine\Test\Entity\TestEntity:
                enabled: true
                ttl: ~
                extra: {}
```

```yml
# Extra keys would be available within API entity cache via CacheConfigurationInterface
api_client:
    cache:
        configuration:
            Bankiru\Api\Doctrine\Test\Entity\TestEntity:
                enabled: true
                ttl: 900
                extra:
                    quick_search: true
```
