<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        "chat_id",
        "name",
        "content",
        "image",
        "cron",
        "enabled",
    ];

    protected $casts = ["enabled" => "boolean"];

    public function scopeEnabled($query): void
    {
        $query->where("enabled", true);
    }

    public function chat()
    {
        return $this->belongsTo(Customer::class, "chat_id", "id");
    }

    public function getContent(): string
    {
        $elements = ["<p>", "</p>"];
        $replaces = ["", "\n"];

        return str_replace($elements, $replaces, $this->content);
    }

    public function getImage(): string|null
    {
        if ($this->image && Storage::disk("tasks")->exists($this->image)) {
            $image_url = Storage::disk("tasks")->url($this->image);

            return $image_url;
        }

        return null;
    }

    protected static function booted(): void
    {
        static::deleted(function (Task $task) {
            $image = $task->image;
            if ($image && Storage::disk("tasks")->exists($image)) {
                Storage::disk("tasks")->delete($image);
            }
        });
        static::updating(function (Task $task) {
            if ($task->isDirty("image")) {
                $image = $task->getOriginal("image");
                if ($image && Storage::disk("tasks")->exists($image)) {
                    Storage::disk("tasks")->delete($image);
                }
            }
        });
    }
}
