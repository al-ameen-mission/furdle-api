import { Icon } from '@iconify/react';

export default function NotFoundPage() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-base-100 px-4">
      <div className="text-center max-w-lg w-full">
        <div className="relative mb-8 mx-auto">
          <div className="w-24 h-24 bg-error/10 rounded-full flex items-center justify-center mx-auto">
            <Icon icon="solar:ufo-broken" className="text-5xl text-error" />
          </div>
        </div>
        <h1 className="text-5xl font-bold text-base-content mb-4">
          404
        </h1>
        <h2 className="text-2xl font-semibold text-base-content/80 mb-6">
          Page Not Found
        </h2>
        <p className="text-base-content/60 mb-8 leading-relaxed text-lg">
          The page you're looking for might have been removed, had its name changed, or is temporarily unavailable.
        </p>
        <button
          onClick={() => window.location.href = '/'}
          className="btn btn-primary btn-outline"
        >
          <Icon icon="hugeicons:home-01" className="text-lg mr-2" />
          Go Back Home
        </button>
      </div>
    </div>
  );
}