<?php

namespace App\Console\Commands;

use App\Models\Rubric;
use App\Models\Statya;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * @codeCoverageIgnore
 */
class ImportCsv extends Command
{
    protected $signature = 'import:csv';
    protected $description = 'Импорт данных из CSV файла';

    public function handle()
    {
        $file = database_path('БД_UTF8.csv');

        if (!file_exists($file)) {
            $this->error('CSV файл не найден! Положите его в папку database/');
            $this->error('Путь: ' . $file);
            return 1;
        }

        $this->info('Начало импорта...');
        
        // Читаем весь файл
        $content = file_get_contents($file);
        
        // Удаляем BOM
        $content = str_replace("\xEF\xBB\xBF", '', $content);
        
        // Разделяем на строки
        $lines = explode("\n", $content);
        
        $count = 0;
        $currentRecord = '';
        
        foreach ($lines as $lineNumber => $line) {
            $line = trim($line);
            
            if (empty($line)) {
                continue;
            }
            
            // Добавляем строку к текущей записи
            if (!empty($currentRecord)) {
                $currentRecord .= "\n" . $line;
            } else {
                $currentRecord = $line;
            }
            
            // Считаем количество разделителей |
            $separatorCount = substr_count($currentRecord, '|');
            
            // Если есть 4 разделителя (5 полей) — запись полная
            if ($separatorCount >= 4) {
                // Разделяем по первому |
                $data = explode('|', $currentRecord, 5);
                
                if (count($data) >= 4) {
                    $title = trim($data[0]);
                    $excerpt = trim($data[1] ?? '');
                    $content = trim($data[2] ?? '');
                    $rubricName = trim($data[3] ?? '');
                    $image = isset($data[4]) ? trim(str_replace('#', '', $data[4])) : null;
                    
                    if (!empty($title) && !empty($rubricName)) {
                        // Находим или создаём рубрику
                        $rubric = Rubric::firstOrCreate(
                            ['name' => $rubricName],
                            ['slug' => Str::slug($rubricName)]
                        );
                        
                        // Создаём статью
                        Statya::create([
                            'title' => $title,
                            'lid' => $rubric->id,
                            'content' => $content,
                            'image' => $image,
                            'rubrics' => $rubricName,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        $count++;
                        $this->info("Статья {$count}: Добавлена '{$title}'");
                    }
                }
                
                // Сбрасываем текущую запись
                $currentRecord = '';
            }
        }

        $this->info("✅ Импорт завершён! Добавлено статей: {$count}");
        return 0;
    }
}