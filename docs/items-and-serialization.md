# Items And Serialization

Drivers store `CacheItem` objects rather than raw application values. A cache item contains the normalized key, serialized payload, TTL, and creation time.

## Cache Items

```php
use CommonPHP\Cache\CacheItem;

$item = CacheItem::create('users.42', 'serialized-payload', 300);

$key = $item->key();
$payload = $item->payload();
$expiresAt = $item->expiresAt();
```

The payload is always a string. The manager serializes values before storing items and unserializes payloads after reading items.

## Immutable Copies

`CacheItem` is readonly. Methods that alter payload or TTL return a new item.

```php
$updated = $item->withPayload('new-payload');
$shortLived = $item->withTtl(30);
```

## Native Serializer

`NativeCacheSerializer` implements `CacheSerializerInterface` using PHP's native serialization functions.

```php
use CommonPHP\Cache\NativeCacheSerializer;

$serializer = new NativeCacheSerializer();

$payload = $serializer->serialize(['name' => 'Ada']);
$value = $serializer->unserialize($payload);
```

Invalid payloads throw `CacheSerializationException`.

## Custom Serializers

Implement `CacheSerializerInterface` when a project needs JSON, igbinary, encryption, compression, or an application-specific value format.

```php
use CommonPHP\Cache\Contracts\CacheSerializerInterface;

final class JsonCacheSerializer implements CacheSerializerInterface
{
    public function serialize(mixed $value): string
    {
        return json_encode($value, JSON_THROW_ON_ERROR);
    }

    public function unserialize(string $payload): mixed
    {
        return json_decode($payload, true, flags: JSON_THROW_ON_ERROR);
    }
}
```

Custom serializers should throw `CacheSerializationException` when they can add useful cache-specific context.
