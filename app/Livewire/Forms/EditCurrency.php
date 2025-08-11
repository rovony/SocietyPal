<?php

namespace App\Livewire\Forms;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditCurrency extends Component
{
    use LivewireAlert;

    public $currency;
    public $currencyName;
    public $currencySymbol;
    public $currencyCode;
    public $currencyPosition;
    public $thousandSeparator;
    public $decimalSeparator;
    public int $numberOfDecimals;

    public function mount()
    {
        $this->currencyName = $this->currency->currency_name;
        $this->currencySymbol = $this->currency->currency_symbol;
        $this->currencyCode = $this->currency->currency_code;
        $this->currencyPosition = $this->currency->currency_position;
        $this->thousandSeparator = $this->currency->thousand_separator ?? ',';
        $this->decimalSeparator = $this->currency->decimal_separator ?? '.';
        $this->numberOfDecimals = $this->currency->no_of_decimal;

    }

    public function submitForm()
    {
        $this->validate([
            'currencyName' => 'required',
            'currencySymbol' => 'required',
            'currencyCode' => 'required',
        ]);

        $this->currency->currency_name = $this->currencyName;
        $this->currency->currency_symbol = $this->currencySymbol;
        $this->currency->currency_code = $this->currencyCode;
        $this->currency->currency_position = $this->currencyPosition;
        $this->currency->thousand_separator = $this->thousandSeparator;
        $this->currency->decimal_separator = $this->decimalSeparator;
        $this->currency->no_of_decimal = $this->numberOfDecimals ?? 0;
        $this->currency->save();

        session()->forget('currency_format_setting' . $this->currency->id);
        $this->dispatch('hideEditCurrency');

        $this->alert('success', __('messages.currencyUpdated'));

        $this->dispatch('hideEditCurrency');
    }

    public function currencyFormat($amount)
    {
        $currency_position = $this->currencyPosition;
        $no_of_decimal = $this->numberOfDecimals ?? 0;
        $thousand_separator = $this->thousandSeparator ?? '';
        $decimal_separator = $this->decimalSeparator ?? '0';
        $currency_symbol = $this->currencySymbol ?? '';

        $amount = number_format($amount, $no_of_decimal, $decimal_separator, $thousand_separator);

        $amount = match ($currency_position) {
            'right' => $amount . $currency_symbol,
            'left_with_space' => $currency_symbol . ' ' . $amount,
            'right_with_space' => $amount . ' ' . $currency_symbol,
            default => $currency_symbol . $amount,
        };

        return $amount;
    }

    public function render()
    {
        return view('livewire.forms.edit-currency', [
            'sampleFormat' => $this->currencyFormat(12345.6789),
        ]);
    }

}
