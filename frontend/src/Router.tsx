
import { BrowserRouter, Routes, Route } from "react-router";
import React from "react";
const RegisterPage = React.lazy(() => import("./pages/register/RegisterPage"));
const NotFoundPage = React.lazy(() => import("./pages/__404"));
export default function Router() {
  return <BrowserRouter >
    <Routes>
      <Route path="/register" element={<RegisterPage />} />
      <Route path="*" element={<NotFoundPage/>} />
    </Routes>

  </BrowserRouter>
}