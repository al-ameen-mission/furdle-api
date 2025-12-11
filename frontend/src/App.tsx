import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { useState, useEffect, useRef } from 'react';
import { ApiUtils } from './utils/api';
import type { ThirdPartyLookupApiResponse } from './@types/types';

function App() {
  const query = new URLSearchParams(window.location.search);
  const formNo: string = query.get('form_no') || '';
  const session: string = query.get('session') || '';
  const queryClient = useQueryClient();

  const dataURLtoFile = (dataurl: string, filename: string): File => {
    const arr = dataurl.split(',');
    const mime = arr[0].match(/:(.*?);/)![1];
    const bstr = atob(arr[1]);
    let n = bstr.length;
    const u8arr = new Uint8Array(n);
    while (n--) {
      u8arr[n] = bstr.charCodeAt(n);
    }
    return new File([u8arr], filename, { type: mime });
  };

  const [iframeHeight, setIframeHeight] = useState('400px');
  const iframeRef = useRef<HTMLIFrameElement>(null);

  const { data: lookupData } = useQuery({
    queryKey: ['hello'],
    queryFn: async () => {
      const res = await fetch(ApiUtils.getApiUrl('/api/third-party'), {
        method: 'POST',
        body: JSON.stringify({ form_no: formNo, session }),
      });
      return res.json() as Promise<ThirdPartyLookupApiResponse>;
    },
  });

  const faceQuery = useQuery({
    queryKey: ['faces', lookupData],
    queryFn: async () => {
      const baseUrl = lookupData?.result.url || '';
      const url = new URL(baseUrl);
      url.pathname += '/faces/search';
      const res = await fetch(url.toString(), {
        method: 'POST',
        body: JSON.stringify({
          query: {
            ...(lookupData?.result.query || {}),
            code: lookupData?.result.student.form_no,
          },
        }),
        headers: {
          'Authorization': `Bearer ${lookupData?.result.token || ''}`,
          'Content-Type': 'application/json',
        },
      });
      return res.json();
    },
    enabled: lookupData !== undefined,
  });

  const deleteMutation = useMutation({
    mutationFn: async (faceId: number) => {
      const baseUrl = lookupData?.result.url || '';
      const url = new URL(baseUrl);
      url.pathname += `/face/${faceId}`;
      const res = await fetch(url.toString(), {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${lookupData?.result.token || ''}`,
        },
      });
      return res.json();
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['faces', lookupData] });
    },
  });

  const registerMutation = useMutation({
    mutationFn: async (image: string) => {
      const baseUrl = lookupData?.result.url || '';
      const url = new URL(baseUrl);
      url.pathname += '/face/register';

      // Prepare payload
      const formData = new FormData();
      const file = dataURLtoFile(image, 'face.jpg');
      formData.append('image', file);
      formData.append('payload', JSON.stringify(lookupData?.result.payload || {}));
      formData.append('query', JSON.stringify(lookupData?.result.query || {}));

      const res = await fetch(url.toString(), {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${lookupData?.result.token || ''}`,
        },
        body: formData,
      });
      return res.json();
    },
  });

  useEffect(() => {
    const handleMessage = async (event: MessageEvent) => {
      if (event.origin === 'https://face.nafish.me') {
        if (event.data.type === 'resize' && event.data.height) {
          setIframeHeight(event.data.height + 'px');
        } else if (event.data.type === 'face-confirmed' && event.data.payload && event.data.payload.image) {
          if (!lookupData) return;
          registerMutation.mutate(event.data.payload.image);
        }
      }
    };
    window.addEventListener('message', handleMessage);
    return () => window.removeEventListener('message', handleMessage);
  }, [lookupData, registerMutation]);


  if (!lookupData) {
    return <div>Loading...</div>;
  }

  if (registerMutation.isSuccess) {
    return (
      <div className="min-h-screen max-w-md mx-auto bg-white shadow-xl px-4 py-6">
        <div className="text-center">
          <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="check-circle" className="w-8 h-8 text-green-600"></i>
          </div>
          <h2 className="text-xl font-bold text-gray-900 mb-2">Registration Successful!</h2>
          <p className="text-gray-600">Your face has been successfully registered.</p>
        </div>
      </div>
    );
  }

  return (

    <div id="registrationContainer" className="min-h-screen max-w-md mx-auto bg-white shadow-xl">

      <div className="bg-indigo-600 text-white px-4 py-5 relative overflow-hidden">
        <div className="absolute inset-0 bg-black opacity-10"></div>
        <div className="relative z-10">
          <h1 className="text-xl font-bold flex items-center">
            <i data-lucide="camera" className="w-6 h-6 mr-2"></i>
            Face Registration
          </h1>
          <p className="text-blue-100 text-sm mt-1">Complete your biometric registration</p>
        </div>
      </div>


      {registerMutation.isPending && (
        <div id="loadingMessage" className="px-4 py-3 bg-blue-50 rounded-lg border border-blue-200 mx-4 mt-4">
          <div className="flex items-center">
            <i data-lucide="loader" className="w-4 h-4 mr-2 text-blue-600 animate-spin"></i>
            <p className="text-sm text-blue-700">Submitting face data...</p>
          </div>
        </div>
      )}

      <div className="px-4 py-6 space-y-6">

        <div className="bg-white border border-gray-200 rounded-xl px-4 py-4">
          <div className="flex items-center space-x-4">
            <div className="relative">
              <img src={lookupData.result.student.image}
                alt="Student Photo"
                className="w-16 h-16 rounded-full object-cover border-3 border-blue-200" />
              <div className="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full border-2 border-white flex items-center justify-center">
                <i data-lucide="check" className="w-3 h-3 text-white"></i>
              </div>
            </div>
            <div className="flex-1">
              <h3 className="font-semibold text-gray-900 text-lg">
                {lookupData.result.student.student_name}
              </h3>
              <div className="space-y-1 mt-1">
                <div className="flex items-center text-sm text-gray-600">
                  <i data-lucide="file-text" className="w-4 h-4 mr-2"></i>
                  Form: {lookupData.result.student.form_no}
                </div>
                <div className="flex items-center text-sm text-gray-600">
                  <i data-lucide="graduation-cap" className="w-4 h-4 mr-2"></i>
                  Class: {lookupData.result.student.class_name}
                </div>
              </div>
            </div>
          </div>
        </div>

        {faceQuery.data && faceQuery.data.result.length > 0 ? (
          <div className="px-4 py-3 bg-yellow-50 rounded-lg border border-yellow-200">
            <h4 className="text-sm font-bold text-yellow-800 mb-2 flex items-center">
              <i data-lucide="alert-triangle" className="w-4 h-4 mr-2 text-yellow-600"></i>
              Face Already Registered
            </h4>
            <p className="text-sm text-yellow-700 mb-2">
              A face matching your biometric data has already been registered in our system. If you believe this is an error, you can choose to delete the existing face data and register a new one.
            </p>
            <button
              onClick={() => {
                const existingFaceId = faceQuery.data?.result[0]?.face_id;
                if (existingFaceId) {
                  deleteMutation.mutate(existingFaceId);
                }
              }}
              className="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors text-sm">
              {deleteMutation.isPending ? 'Deleting...' : 'Delete Existing Face and Register New'}
            </button>

          </div>
        ) : (
          <>

          <div className="bg-white border-2 border-dashed border-gray-300 rounded-xl py-4">
            <div className="bg-white overflow-hidden">
              <iframe id="captureFrame"
                ref={iframeRef}
                src="https://face.nafish.me/frame/capture"
                className="w-full border-0"
                style={{ height: iframeHeight }}
                onLoad={() => {
                  iframeRef.current?.contentWindow?.postMessage({
                    type: 'getHeight'
                  }, '*');
                }}
                allow="camera; microphone; autoplay"></iframe>
            </div>

          </div>

          <div className="px-4 py-3 bg-red-50 rounded-lg border border-red-200 mt-4">
            <h4 className="text-sm font-bold text-red-800 mb-2 flex items-center">
              <i data-lucide="alert-triangle" className="w-4 h-4 mr-2 text-red-600"></i>
              Critical Instructions
            </h4>
            <ul className="text-sm text-red-700 space-y-1">
              <li className="flex items-start">
                <i data-lucide="x-circle" className="w-4 h-4 mr-2 text-red-600 mt-0.5 shrink-0"></i>
                Ensure you are in a well-lit area.
              </li>
              <li className="flex items-start">
                <i data-lucide="x-circle" className="w-4 h-4 mr-2 text-red-600 mt-0.5 shrink-0"></i>
                Position your face within the frame displayed in the capture area.
              </li>
              <li className="flex items-start">
                <i data-lucide="x-circle" className="w-4 h-4 mr-2 text-red-600 mt-0.5 shrink-0"></i>
                Avoid wearing hats, sunglasses, or anything that may obscure your face.
              </li>
              <li className="flex items-start">
                <i data-lucide="x-circle" className="w-4 h-4 mr-2 text-red-600 mt-0.5 shrink-0"></i>
                Follow the on-screen prompts to capture your face.
              </li>
            </ul>
          </div>
          </>
        )}



      </div>
    </div>
  )
}

export default App
