import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { useState, useEffect, useRef } from 'react';
import { Icon } from '@iconify/react';
import { apiService } from '../../utils/api';

// Utility function to convert data URL to File
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

function RegisterPage() {
  const query = new URLSearchParams(window.location.search);
  const formNo: string = query.get('form_no') || '';
  const session: string = query.get('session') || '';
  const redirect: string = query.get('redirect') || '';
  const queryClient = useQueryClient();

  const [iframeHeight, setIframeHeight] = useState('400px');
  const iframeRef = useRef<HTMLIFrameElement>(null);

  const lookupQuery = useQuery({
    queryKey: ['third-party-lookup', formNo, session],
    queryFn: () => apiService.thirdPartyLookup(formNo, session),
    enabled: !!formNo && !!session,
  });

  const faceQuery = useQuery({
    queryKey: ['faces', lookupQuery.data?.result.student.form_no],
    queryFn: () => {
      if (!lookupQuery.data) throw new Error('Lookup data not available');
      const query = {
        ...(lookupQuery.data.result.query || {}),
        code: lookupQuery.data.result.student.form_no,
      };
      return apiService.searchFaces(
        lookupQuery.data.result.url,
        lookupQuery.data.result.token,
        query
      );
    },
    enabled: !!lookupQuery.data,
  });

  const deleteMutation = useMutation({
    mutationFn: async (faceId: number) => {
      if (!lookupQuery.data) throw new Error('Lookup data not available');
      return apiService.deleteFace(
        lookupQuery.data.result.url,
        lookupQuery.data.result.token,
        faceId
      );
    },
    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: ['faces', lookupQuery.data?.result.student.form_no]
      });
    },
  });

  const registerMutation = useMutation({
    mutationFn: async (image: string) => {
      if (!lookupQuery.data) throw new Error('Lookup data not available');

      const file = dataURLtoFile(image, 'face.jpg');
      return apiService.registerFace(
        lookupQuery.data.result.url,
        lookupQuery.data.result.token,
        file,
        lookupQuery.data.result.payload || {},
        lookupQuery.data.result.query || {},
        lookupQuery.data.result.uquery || {}
      );
    },
  });

  useEffect(() => {
    const handleMessage = async (event: MessageEvent) => {
      if (event.origin === 'https://face.nafish.me') {
        if (event.data.type === 'resize' && event.data.height) {
          setIframeHeight(event.data.height + 'px');
        } else if (event.data.type === 'face-confirmed' && event.data.payload && event.data.payload.image) {
          if (!lookupQuery.data) return;
          registerMutation.mutate(event.data.payload.image);
        }
      }
    };
    window.addEventListener('message', handleMessage);
    return () => window.removeEventListener('message', handleMessage);
  }, [lookupQuery.data, registerMutation]);

  useEffect(() => {
    const interval = setInterval(() => {
      iframeRef.current?.contentWindow?.postMessage(
        { type: 'getHeight' },
        '*'
      );
    }, 1000);
    return () => clearInterval(interval);
  }, []);

  useEffect(() => {
    if (registerMutation.isSuccess && redirect) {
      setTimeout(() => {
        window.location.href = redirect;
      }, 3000);
    }
  }, [registerMutation.isSuccess, redirect]);


  // Early return for loading state
  if (!formNo || !session) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-base-100">
        <div className="text-center max-w-md px-6">
          <div className="w-16 h-16 bg-warning/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <Icon icon="hugeicons:alert-circle" className="text-2xl text-warning" />
          </div>
          <h2 className="text-xl font-bold text-base-content mb-2">Invalid Access</h2>
          <p className="text-base-content/70">Form number and session are required to access this page.</p>
        </div>
      </div>
    );
  }

  if (lookupQuery.isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-base-100">
        <div className="text-center">
          <div className="loading loading-spinner loading-lg text-primary mx-auto mb-4"></div>
          <p className="text-base-content/70">Loading...</p>
        </div>
      </div>
    );
  }


  if (!lookupQuery.data) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-base-100">
        <div className="text-center max-w-md px-6">
          <div className="w-16 h-16 bg-error/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <Icon icon="hugeicons:close-circle" className="text-2xl text-error" />
          </div>
          <h2 className="text-xl font-bold text-base-content mb-2">Access Denied</h2>
          <p className="text-base-content/70">{'Unable to verify your session.'}</p>
        </div>
      </div>
    );
  }


  if (registerMutation.isSuccess) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-base-100 px-4">
        <div className="max-w-md w-full bg-base-200 rounded-2xl shadow-xl p-8 text-center">
          <div className="w-20 h-20 bg-success/20 rounded-full flex items-center justify-center mx-auto mb-6">
            <Icon icon="hugeicons:check-circle" className="text-3xl text-success" />
          </div>
          <h2 className="text-2xl font-bold text-base-content mb-3">Registration Successful!</h2>
          <p className="text-base-content/70 leading-relaxed mb-4">Your face has been successfully registered in our system.</p>
          {redirect && (
            <p className="text-sm text-base-content/50">You will be redirected in 3 seconds...</p>
          )}
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-base-100 relative">
      <div className="max-w-md mx-auto bg-base-100 shadow-xl min-h-screen">
        {/* Header */}
        <header className="bg-primary text-primary-content px-6 py-8 relative overflow-hidden">
          <div className="absolute inset-0 bg-black opacity-10"></div>
          <div className="relative z-10">
            <div className="flex items-center mb-2">
              <div className="w-10 h-10 bg-primary-content/20 rounded-full flex items-center justify-center mr-3">
                <Icon icon="hugeicons:camera-01" className="text-xl text-primary-content" />
              </div>
              <h1 className="text-2xl font-bold">Face Registration</h1>
            </div>
            <p className="text-primary-content/80 text-sm leading-relaxed">Complete your biometric registration securely</p>
          </div>
        </header>

        {/* Error States */}
        {registerMutation.isError && (
          <div className="px-6 py-4 bg-error/10 border-b border-error/20">
            <div className="flex items-center">
              <Icon icon="hugeicons:alert-circle" className="text-xl text-error mr-3" />
              <p className="text-sm text-error">Failed to register face. Please try again.</p>
            </div>
          </div>
        )}

        {deleteMutation.isError && (
          <div className="px-6 py-4 bg-error/10 border-b border-error/20">
            <div className="flex items-center">
              <Icon icon="hugeicons:alert-circle" className="text-xl text-error mr-3" />
              <p className="text-sm text-error">Failed to delete existing face. Please try again.</p>
            </div>
          </div>
        )}

        {/* Main Content */}
        <main className="px-6 py-6 space-y-6">
          {/* Student Info Card */}
          <div className="bg-base-200 border border-base-300 rounded-2xl p-5">
            <div className="flex items-center space-x-4">
              <div className="relative">
                <img
                  src={lookupQuery.data.result.student.image}
                  alt="Student Photo"
                  className="w-16 h-16 rounded-full object-cover border-2 border-primary/20"
                />
                <div className="absolute -bottom-1 -right-1 w-6 h-6 bg-success rounded-full border-2 border-base-100 flex items-center justify-center">
                  <Icon icon="hugeicons:checkmark" className="text-xs text-success-content" />
                </div>
              </div>
              <div className="flex-1">
                <h3 className="font-semibold text-base-content text-lg mb-1">
                  {lookupQuery.data.result.student.student_name}
                </h3>
                <div className="space-y-1">
                  <div className="flex items-center text-sm text-base-content/70">
                    <Icon icon="hugeicons:file-01" className="text-base mr-2" />
                    Form: {lookupQuery.data.result.student.form_no}
                  </div>
                  <div className="flex items-center text-sm text-base-content/70">
                    <Icon icon="hugeicons:school" className="text-base mr-2" />
                    Class: {lookupQuery.data.result.student.class_name}
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Face Registration Section */}
          {faceQuery.data && faceQuery.data.result && faceQuery.data.result.records.length > 0 ? (
            <div className="card bg-warning/5 border border-warning/20">
              <div className="card-body p-6">
                <div className="flex items-start gap-3 mb-4">
                  <Icon icon="hugeicons:alert-triangle" className="text-2xl text-warning flex-shrink-0 mt-1" />
                  <div>
                    <h4 className="card-title text-warning text-lg mb-2">Face Already Registered</h4>
                    <p className="text-base-content/70 text-sm leading-relaxed">
                      A face matching your biometric data has already been registered. If you believe this is an error, you can delete the existing data and register a new one.
                    </p>
                  </div>
                </div>
                <div className="card-actions justify-end">
                  <button
                    onClick={() => {
                      if (window.confirm('Are you sure you want to delete the existing face data?')) {
                        const records = faceQuery.data!.result.records;
                        for (const record of records) {
                          deleteMutation.mutate(record.id);
                        }
                      }
                    }}
                    disabled={deleteMutation.isPending}
                    className="btn btn-warning btn-outline"
                  >
                    {deleteMutation.isPending ? (
                      <>
                        <span className="loading loading-spinner loading-sm"></span>
                        Deleting...
                      </>
                    ) : (
                      <>
                        <Icon icon="hugeicons:delete-01" className="text-base" />
                        Delete & Register New
                      </>
                    )}
                  </button>
                </div>
              </div>
            </div>
          ) : (
            <>
              {/* Face Capture */}
              <div className="bg-base-100 border-2 border-dashed border-base-300 rounded-2xl p-4">
                <div>
                  <iframe
                    id="captureFrame"
                    ref={iframeRef}
                    src="https://face.nafish.me/frame/capture"
                    className="w-full border-0"
                    style={{ height: iframeHeight }}
                    onLoad={() => {
                      iframeRef.current?.contentWindow?.postMessage(
                        { type: 'getHeight' },
                        '*'
                      );
                    }}
                    allow="camera; microphone; autoplay"
                  />
                </div>
              </div>

              {/* Instructions */}
              <div className="bg-error/10 border border-error/20 rounded-2xl p-5">
                <div className="flex items-center mb-3">
                  <Icon icon="hugeicons:alert-triangle" className="text-xl text-error mr-3" />
                  <h4 className="text-sm font-bold text-error">Important Instructions</h4>
                </div>
                <ul className="text-sm text-error/80 space-y-2">
                  <li className="flex items-start">
                    <Icon icon="hugeicons:dot" className="text-base text-error mr-3 mt-0.5 shrink-0" />
                    Ensure you are in a well-lit area
                  </li>
                  <li className="flex items-start">
                    <Icon icon="hugeicons:dot" className="text-base text-error mr-3 mt-0.5 shrink-0" />
                    Position your face within the capture frame
                  </li>
                  <li className="flex items-start">
                    <Icon icon="hugeicons:dot" className="text-base text-error mr-3 mt-0.5 shrink-0" />
                    Remove hats, sunglasses, or anything obscuring your face
                  </li>
                  <li className="flex items-start">
                    <Icon icon="hugeicons:dot" className="text-base text-error mr-3 mt-0.5 shrink-0" />
                    Follow the on-screen prompts to complete capture
                  </li>
                </ul>
              </div>
            </>
          )}
        </main>
      </div>

      {/* Loading Overlay */}
      {(registerMutation.isPending || deleteMutation.isPending) && (
        <div className="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="text-center">
            <div className="w-12 h-12 border-4 border-white border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p className="text-white text-lg font-medium">
              {registerMutation.isPending ? 'Submitting face data...' : 'Deleting existing face...'}
            </p>
          </div>
        </div>
      )}
    </div>
  );
}

export default RegisterPage
