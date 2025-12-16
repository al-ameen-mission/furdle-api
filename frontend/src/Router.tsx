
import { BrowserRouter, Routes, Route } from "react-router";
import React, { Suspense } from "react";
const RegisterPage = React.lazy(() => import("./pages/register/RegisterPage"));
const NotFoundPage = React.lazy(() => import("./pages/__404"));

import { Icon } from '@iconify/react';

function LoadingFallback() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-base-100">
      <div className="text-center">
        <div className="relative mb-6 w-20 h-20 mx-auto">
          <div className="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center animate-pulse">
            <Icon icon="hugeicons:camera-01" className="text-4xl text-primary" />
          </div>
          <div className="absolute inset-0 rounded-full border-4 border-primary/20 animate-ping"></div>
        </div>
        
      </div>
    </div>
  );
}

export default function Router() {
  return (
    <BrowserRouter>
      <Suspense fallback={<LoadingFallback />}>
        <Routes>
          <Route path="/register" element={<RegisterPage />} />
          <Route path="*" element={<NotFoundPage />} />
        </Routes>
      </Suspense>
    </BrowserRouter>
  );
}