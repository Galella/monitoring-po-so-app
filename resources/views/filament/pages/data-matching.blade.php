<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Data Matching Content --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            {{-- Tabs --}}
            <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                <x-filament::tabs label="Data Tabs">
                    @foreach ($this->getTabs() as $tabKey => $tab)
                        <x-filament::tabs.item
                            :active="$activeTab === $tabKey"
                            wire:click="setActiveTab('{{ $tabKey }}')"
                            :icon="$tab['icon']"
                            :badge="$tab['badge']"
                            :badge-color="$tab['badgeColor']"
                        >
                            {{ $tab['label'] }}
                        </x-filament::tabs.item>
                    @endforeach
                </x-filament::tabs>
            </div>

            {{-- Table --}}
            <div class="p-2 p-4">
                {{ $this->table }}
            </div>
        </div>
    </div>
</x-filament-panels::page>