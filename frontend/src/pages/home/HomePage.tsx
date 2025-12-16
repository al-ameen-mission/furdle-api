import { Icon } from '@iconify/react';

export default function HomePage() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-base-100 px-4">
      <div className="text-center max-w-lg w-full">
        <div className="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
          <Icon icon="hugeicons:camera-01" className="text-4xl text-primary" />
        </div>
        <h1 className="text-4xl font-bold text-base-content mb-4">
          Al-Ameen Face
        </h1>
        <p className="text-base-content/70 text-lg mb-8">
          Secure biometric registration system
        </p>
        <a
          href="/register"
          className="btn btn-primary btn-lg"
        >
          <Icon icon="hugeicons:user-add-01" className="text-xl mr-2" />
          Start Registration
        </a>
      </div>
    </div>
  );
}