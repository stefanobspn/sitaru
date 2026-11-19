<div class="flex min-h-screen items-center justify-center px-4 py-8">
    <div class="w-full max-w-sm rounded-sm border border-slate-300 p-5 sm:p-6">
        <h1 class="text-center text-xl font-semibold mb-4">Login Admin</h1>
        <form wire:submit="login" class="space-y-4">
            <x-input name="email" wire:model="email" type="email" legend="Email" required />
            <x-input name="password" wire:model="password" type="password" legend="Password" placeholder="••••••••" required />

            <button type="submit" class="btn btn-primary w-full mt-2">
                <span wire:loading.remove wire:target="login">Login</span>
                <span wire:loading wire:target="login">Memproses...</span>
            </button>
        </form>
    </div>
</div>
