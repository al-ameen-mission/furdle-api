import React from 'react';
import { Icon } from '@iconify/react';

export default function NotFoundPage() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-base-100 px-4">
      <div className="card bg-base-200 border border-base-300 max-w-md w-full">
        <div className="card-body text-center p-8">
          <div className="w-20 h-20 bg-error/20 rounded-full flex items-center justify-center mx-auto mb-6">
            <Icon icon="hugeicons:close-circle" className="text-4xl text-error" />
          </div>
          <h1 className="card-title text-3xl font-bold text-base-content mb-4">
            404 - Page Not Found
          </h1>
          <p className="text-base-content/70 mb-6 leading-relaxed">
            The page you're looking for might have been removed, had its name changed, or is temporarily unavailable.
          </p>
          <div className="card-actions justify-center">
            <button
              onClick={() => window.location.href = '/'}
              className="btn btn-primary btn-lg"
            >
              <Icon icon="hugeicons:home-01" className="text-lg mr-2" />
              Go Back Home
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}