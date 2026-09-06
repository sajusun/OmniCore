<?php

namespace App\Modules\AI\Enums;

enum AiProvider: string
{
    case GEMINI = 'gemini';
    case OPENAI = 'openai';
    case KNOWLEDGE_BASE = 'knowledge_base';
    case MOCK = 'mock';

    public function label(): string
    {
        return match ($this) {
            self::GEMINI => 'Google Gemini AI',
            self::OPENAI => 'OpenAI ChatGPT',
            self::KNOWLEDGE_BASE => 'Knowledge Base Matcher',
            self::MOCK => 'Mock Engine',
        };
    }
}
