import { Icon } from '@iconify/react';

export function HomePage() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-base-100">
      <div className="text-center max-w-md px-6">
        <div className="w-16 h-16 bg-error/20 rounded-full flex items-center justify-center mx-auto mb-4">
          <Icon icon="hugeicons:close-circle" className="text-2xl text-error" />
        </div>
        <h2 className="text-xl font-bold text-base-content mb-2">Not Allowed</h2>
        <p className="text-base-content/70">You do not have permission to access this page.</p>
      </div>
    </div>
  );
}