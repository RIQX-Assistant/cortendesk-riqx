<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesConsole;
use App\Models\ConsoleAudit;
use App\Models\RiqxAd;
use App\Models\Setting;
use App\Support\LocalDate;
use App\Support\RiqxAds;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * RIQX: Ads — what the RIQX app shows in ADS1 (bottom of the left column) and
 * ADS2 (banner above the status bar; the Android bottom banner).
 *
 * Same trust level as Client Downloads (content every app shows), so it reuses
 * the `setting` area: mount() checks View, every mutator re-checks Manage.
 */
class RiqxAdManager extends Component
{
    use AuthorizesConsole;
    use WithFileUploads;

    public bool $showModal = false;

    public ?int $editing = null;

    /** The staged upload (TemporaryUploadedFile) or null. */
    public $file = null;

    /** Not "slot": Livewire reserves $slot in component views. */
    public string $adSlot = 'ads1';

    public string $title = '';

    public string $linkUrl = '';

    public string $locale = 'all';

    /** datetime-local values, in the display timezone. */
    public string $startsAt = '';

    public string $endsAt = '';

    public int $durationSeconds = 8;

    public bool $isActive = true;

    public function mount(): void
    {
        $this->authorizeConsole('setting', 'r');
    }

    public function create(string $slot = 'ads1'): void
    {
        $this->authorizeConsole('setting', 'rw');
        $this->resetForm();
        $this->adSlot = in_array($slot, RiqxAd::SLOTS, true) ? $slot : 'ads1';
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorizeConsole('setting', 'rw');
        $this->resetForm();

        $ad = RiqxAd::findOrFail($id);
        $this->editing = $ad->id;
        $this->adSlot = $ad->slot;
        $this->title = $ad->title;
        $this->linkUrl = $ad->link_url ?? '';
        $this->locale = $ad->locale;
        $this->startsAt = $this->toLocalInput($ad->starts_at);
        $this->endsAt = $this->toLocalInput($ad->ends_at);
        $this->durationSeconds = $ad->duration_seconds;
        $this->isActive = $ad->is_active;
        $this->showModal = true;
    }

    public function updatedFile(): void
    {
        $this->authorizeConsole('setting', 'rw');
        if ($this->file && trim($this->title) === '') {
            $this->title = Str::limit(pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME), 120, '');
        }
    }

    public function save(): void
    {
        $this->authorizeConsole('setting', 'rw');

        $maxKb = self::maxKilobytes();

        $this->validate([
            'file' => [$this->editing ? 'nullable' : 'required', 'file', 'max:'.$maxKb, function ($attribute, $value, $fail) {
                if ($value && ! array_key_exists(self::extensionOf($value->getClientOriginalName()), RiqxAd::TYPES)) {
                    $fail(__('Allowed file types: :types.', ['types' => implode(', ', array_keys(RiqxAd::TYPES))]));
                }
            }],
            'adSlot' => ['required', 'in:'.implode(',', RiqxAd::SLOTS)],
            'title' => ['required', 'string', 'max:120'],
            'linkUrl' => ['nullable', 'url:http,https', 'max:500'],
            'locale' => ['required', 'in:'.implode(',', RiqxAd::LOCALES)],
            'startsAt' => ['nullable', 'date'],
            'endsAt' => ['nullable', 'date', 'after:startsAt'],
            'durationSeconds' => ['required', 'integer', 'min:3', 'max:300'],
        ], [
            'file.max' => __('That file is larger than the :size MB limit.', ['size' => (int) round($maxKb / 1024)]),
        ], [
            'file' => __('File'),
            'linkUrl' => __('Link'),
            'startsAt' => __('Start'),
            'endsAt' => __('End'),
            'durationSeconds' => __('Display time'),
        ]);

        $ad = $this->editing ? RiqxAd::findOrFail($this->editing) : new RiqxAd;
        $previous = $ad->filename;

        if ($this->file) {
            $original = $this->file->getClientOriginalName();
            $ext = self::extensionOf($original);
            [$type, $mime] = RiqxAd::TYPES[$ext];
            $stored = 'ad-'.Str::lower(Str::random(20)).'.'.$ext;

            $this->file->storeAs('', $stored, ['disk' => RiqxAd::DISK]);
            $path = Storage::disk(RiqxAd::DISK)->path($stored);

            $width = $height = null;
            if ($type !== 'video' && ($info = @getimagesize($path))) {
                [$width, $height] = [$info[0], $info[1]];
            }

            $ad->fill([
                'media_type' => $type,
                'mime' => $mime,
                'filename' => $stored,
                'original_name' => Str::limit($original, 250, ''),
                'size' => (int) filesize($path),
                'sha256' => hash_file('sha256', $path),
                'width' => $width,
                'height' => $height,
            ]);
        }

        $ad->fill([
            'slot' => $this->adSlot,
            'title' => trim($this->title),
            'link_url' => trim($this->linkUrl) !== '' ? trim($this->linkUrl) : null,
            'locale' => $this->locale,
            'starts_at' => $this->fromLocalInput($this->startsAt),
            'ends_at' => $this->fromLocalInput($this->endsAt),
            'duration_seconds' => $this->durationSeconds,
            'is_active' => $this->isActive,
        ]);

        if (! $this->editing) {
            $ad->uploaded_by = auth()->id();
            $ad->sort_order = (int) RiqxAd::where('slot', $this->adSlot)->max('sort_order') + 1;
        }

        $ad->save();

        if ($previous && $previous !== $ad->filename) {
            Storage::disk(RiqxAd::DISK)->delete($previous);
        }

        ConsoleAudit::record(
            $this->editing ? 'riqx_ad.update' : 'riqx_ad.create',
            ($this->editing ? 'Updated' : 'Uploaded').' ad '.$ad->title.' ('.strtoupper($ad->slot).')',
            'riqx_ad',
            (string) $ad->id,
        );

        $this->closeModal();
    }

    public function toggleActive(int $id): void
    {
        $this->authorizeConsole('setting', 'rw');

        $ad = RiqxAd::findOrFail($id);
        $ad->update(['is_active' => ! $ad->is_active]);

        ConsoleAudit::record('riqx_ad.'.($ad->is_active ? 'enable' : 'disable'),
            ($ad->is_active ? 'Enabled' : 'Disabled').' ad '.$ad->title, 'riqx_ad', (string) $ad->id);
    }

    public function move(int $id, string $direction): void
    {
        $this->authorizeConsole('setting', 'rw');

        $ad = RiqxAd::findOrFail($id);
        $ordered = RiqxAd::where('slot', $ad->slot)->orderBy('sort_order')->orderBy('id')->get()->values();
        $index = $ordered->search(fn (RiqxAd $a) => $a->id === $id);
        $target = $direction === 'up' ? $index - 1 : $index + 1;

        if ($index === false || $target < 0 || $target >= $ordered->count()) {
            return;
        }

        $list = $ordered->all();
        [$list[$index], $list[$target]] = [$list[$target], $list[$index]];
        foreach ($list as $position => $row) {
            $row->update(['sort_order' => $position + 1]);
        }
    }

    public function deleteAd(int $id): void
    {
        $this->authorizeConsole('setting', 'rw');

        $ad = RiqxAd::findOrFail($id);
        $title = $ad->title;
        $ad->delete(); // the model's deleting hook removes the file

        ConsoleAudit::record('riqx_ad.delete', 'Deleted ad '.$title, 'riqx_ad', (string) $id);
    }

    /** Tell every app to re-check now (their next poll sees a new ETag). */
    public function publishNow(): void
    {
        $this->authorizeConsole('setting', 'rw');

        RiqxAds::publishNow();
        ConsoleAudit::record('riqx_ad.publish', 'Published ads to the RIQX apps', 'riqx_ad', null);

        session()->flash('riqx_ads_status', __('Published. Each RIQX app picks it up at its next check (at most :minutes minutes, or when its window is activated).', [
            'minutes' => (int) ceil(config('riqx.ads_poll_seconds', 600) / 60),
        ]));
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset('editing', 'file', 'title', 'linkUrl', 'startsAt', 'endsAt');
        $this->adSlot = 'ads1';
        $this->locale = 'all';
        $this->durationSeconds = 8;
        $this->isActive = true;
        $this->resetValidation();
    }

    private function toLocalInput(?Carbon $at): string
    {
        return $at ? $at->copy()->setTimezone(LocalDate::timezone())->format('Y-m-d\TH:i') : '';
    }

    private function fromLocalInput(string $value): ?Carbon
    {
        return trim($value) === '' ? null : Carbon::parse($value, LocalDate::timezone())->utc();
    }

    private static function extensionOf(string $name): string
    {
        return strtolower(pathinfo($name, PATHINFO_EXTENSION));
    }

    public static function maxKilobytes(): int
    {
        return max(1024, (int) config('riqx.ads_max_mb', 50) * 1024);
    }

    public function render()
    {
        $ads = RiqxAd::ordered()->get();

        return view('livewire.riqx-ad-manager', [
            'adsBySlot' => collect(RiqxAd::SLOTS)->mapWithKeys(fn ($s) => [$s => $ads->where('slot', $s)->values()]),
            'sizes' => RiqxAd::SIZES,
            'maxMb' => (int) round(self::maxKilobytes() / 1024),
            'manifestUrl' => url('/api/riqx/ads'),
            'publishedAt' => Setting::get(RiqxAds::PUBLISHED_KEY),
            'canManage' => $this->consoleAllows('setting', 'rw'),
        ]);
    }
}
