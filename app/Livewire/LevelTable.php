<?php

namespace App\Livewire;

use App\Models\Level;
use Livewire\Component;
use Livewire\WithPagination;

class LevelTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $difficulty = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'difficulty' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDifficulty(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
    }

    public function togglePublic(int $id): void
    {
        $level = Level::find($id);

        if ($level) {
            $level->update(['is_public' => !$level->is_public]);
        }
    }

    public function deleteLevel(int $id): void
    {
        $level = Level::find($id);

        if ($level) {
            $level->delete();
            session()->flash('message', 'Level deleted.');
        }
    }

    public function render()
    {
        $query = Level::with('user');

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Difficulty filter
        if ($this->difficulty) {
            $query->where('difficulty', $this->difficulty);
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDir);

        $levels = $query->paginate(10);

        return view('livewire.level-table', compact('levels'));
    }
}
