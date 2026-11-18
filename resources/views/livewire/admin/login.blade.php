<div class="flex items-center justify-center min-h-screen">
    <div class="border border-slate-300 w-80 p-6 rounded-sm">
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
