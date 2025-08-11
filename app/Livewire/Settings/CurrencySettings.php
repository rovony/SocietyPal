<?php

namespace App\Livewire\Settings;

use App\Models\Currency;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;

use Livewire\Component;

class CurrencySettings extends Component
{
    use LivewireAlert;

    protected $listeners = ['refreshCurrencies' => 'mount'];

    public $currencies;
    public $currency;
    public $showEditCurrencyModal = false;
    public $showAddCurrencyModal = false;
    public $confirmDeleteCurrencyModal = false;

    public function mount()
    {
        $this->currencies = Currency::get();
    }

    public function showAddCurrency()
    {
        $this->showAddCurrencyModal = true;
    }

    public function showEditCurrency($id)
    {
        $this->currency = Currency::findOrFail($id);
        $this->showEditCurrencyModal = true;
    }

    public function showDeleteCurrency($id)
    {
        $this->currency = Currency::findOrFail($id);
        $this->confirmDeleteCurrencyModal = true;
    }

    public function deleteCurrency($id)
    {
        Currency::destroy($id);

        $this->confirmDeleteCurrencyModal = false;

        $this->currency= '';
        $this->dispatch('refreshCurrencies');

        $this->alert('success', __('messages.currencyDeleted'));
    }

    #[On('hideEditCurrency')]
    public function hideEditCurrency()
    {
        $this->showEditCurrencyModal = false;
        $this->dispatch('refreshCurrencies');
    }

    #[On('hideAddCurrency')]
    public function hideAddCurrency()
    {
        $this->showAddCurrencyModal = false;
        $this->dispatch('refreshCurrencies');
    }

    public function render()
    {
        return view('livewire.settings.currency-settings');
    }

}
