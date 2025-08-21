{{-- resources/views/admin/bank_accounts/create.blade.php --}}
<x-admin-layout
	title="Nueva Cuenta Bancaria | Betegar"
	:breadcrumbs="[
		['name' => 'Dashboard', 'href' => route('admin.dashboard')],
		['name' => 'Cuentas Bancarias', 'href' => route('admin.bank-accounts.index')],
		['name' => 'Nueva cuenta'],
	]"
>
	@livewire('admin.bank-account-create')
</x-admin-layout>
