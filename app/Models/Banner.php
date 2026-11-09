<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'media_type',
        'video_url',
        'video_file',
        'image_url',
        'image_file',
        'btn_text',
        'btn_link',
        'is_active',
        'position',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position' => 'integer',
    ];

    public function isVideo(): bool
    {
        return $this->media_type === 'video' && (!empty($this->video_url) || !empty($this->video_file));
    }

    public function isDirectVideo(): bool
    {
        if (!empty($this->video_file)) {
            return true;
        }

        if (!empty($this->video_url)) {
            $path = parse_url($this->video_url, PHP_URL_PATH) ?? '';
            return preg_match('/\.(mp4|webm|ogg|mov)$/i', $path);
        }

        return false;
    }

    public function getEffectiveVideoUrlAttribute(): string
    {
        if (!empty($this->video_file)) {
            return asset('storage/' . $this->video_file);
        }

        if (empty($this->video_url)) {
            return '';
        }

        return self::formatYoutubeUrl($this->video_url);
    }

    public function getEffectiveImageUrlAttribute(): string
    {
        if (!empty($this->image_file)) {
            return asset('storage/' . $this->image_file);
        }

        return $this->image_url ?: '';
    }

    public static function extractYoutubeId(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/(?:watch\?.*v=|embed\/|shorts\/)|youtu\.be\/)([^& \? \n]+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public static function formatYoutubeUrl(?string $url): string
    {
        if (empty($url)) {
            return '';
        }

        $videoId = self::extractYoutubeId($url);
        if ($videoId) {
            return "https://www.youtube-nocookie.com/embed/{$videoId}?autoplay=1&mute=1&controls=0&showinfo=0&rel=0&disablekb=1&iv_load_policy=3&modestbranding=1&playsinline=1&fs=0&autohide=1&enablejsapi=1";
        }

        if (str_contains($url, '/embed/')) {
            if (!str_contains($url, 'autoplay=')) {
                $separator = str_contains($url, '?') ? '&' : '?';
                $url .= $separator . 'autoplay=1&mute=1&controls=0&showinfo=0&rel=0&disablekb=1&iv_load_policy=3&modestbranding=1&fs=0&playsinline=1&enablejsapi=1';
            }
            return $url;
        }

        return $url;
    }
}

