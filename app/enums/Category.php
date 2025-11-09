<?php
namespace app\enums;

enum Category: string {
    case Family = 'família';
    case Work = 'trabalho';
    case Friends = 'amigos';
    case Customer = 'cliente';
    case Supplier = 'fornecedor';
    case Others = 'outros';

    // Valor padrão
    public static function default(): self {
        return self::Others;
    }

    public static function fromValue(string $value): self {
        return self::tryFrom($value) ?? self::default();
    }

}