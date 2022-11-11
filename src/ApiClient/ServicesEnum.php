<?php

namespace App\ApiClient;

enum ServicesEnum: string
{
    const GAG = "9gag";
    const AMAZON = "amazon";
    const BILIBILI = "bilibili";
    const CLOUDFLARE = "cloudflare";
    const DAILYMOTION = "dailymotion";
    const DEEZER = "deezer";
    const DISCORD = "discord";
    const DISNEYPLUS = "disneyplus";
    const DOUBAN = "douban";
    const EBAY = "ebay";
    const EPIC_GAMES = "epic_games";
    const FACEBOOK = "facebook";
    const HULU = "hulu";
    const ICLOUD_PRIVATE_RELAY = "icloud_private_relay";
    const IMGUR = "imgur";
    const INSTAGRAM = "instagram";
    const MAIL_RU = "mail_ru";
    const MINECRAFT = "minecraft";
    const NETFLIX = "netflix";
    const OK = "ok";
    const ORIGIN = "origin";
    const PINTEREST = "pinterest";
    const QQ = "qq";
    const REDDIT = "reddit";
    const ROBLOX = "roblox";
    const SKYPE = "skype";
    const SNAPCHAT = "snapchat";
    const SPOTIFY = "spotify";
    const STEAM = "steam";
    const TELEGRAM = "telegram";
    const TIKTOK = "tiktok";
    const TINDER = "tinder";
    const TWITCH = "twitch";
    const TWITTER = "twitter";
    const VIBER = "viber";
    const VIMEO = "vimeo";
    const VK = "vk";
    const WECHAT = "wechat";
    const WEIBO = "weibo";
    const WHATSAPP = "whatsapp";
    const YOUTUBE = "youtube";
    const ZHIHU = "zhihu";

    const ALL = [
        self::YOUTUBE,
        self::GAG,
        self::AMAZON,
        self::BILIBILI,
        self::CLOUDFLARE,
        self::DAILYMOTION,
        self::DEEZER,
        self::DISCORD,
        self::DISNEYPLUS,
        self::DOUBAN,
        self::EBAY,
        self::EPIC_GAMES,
        self::FACEBOOK,
        self::HULU,
        self::ICLOUD_PRIVATE_RELAY,
        self::IMGUR,
        self::INSTAGRAM,
        self::MAIL_RU,
        self::MINECRAFT,
        self::NETFLIX,
        self::OK,
        self::ORIGIN,
        self::PINTEREST,
        self::QQ,
        self::REDDIT,
        self::ROBLOX,
        self::SKYPE,
        self::SNAPCHAT,
        self::SPOTIFY,
        self::STEAM,
        self::TELEGRAM,
        self::TIKTOK,
        self::TINDER,
        self::TWITCH,
        self::TWITTER,
        self::VIBER,
        self::VIMEO,
        self::VK,
        self::WECHAT,
        self::WEIBO,
        self::WHATSAPP,
        self::YOUTUBE,
        self::ZHIHU,
    ];
}
