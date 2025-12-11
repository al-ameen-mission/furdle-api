import { useQuery } from "@tanstack/react-query"
import { ApiUtils } from "./utils/api"

function App() {
  const query = new URLSearchParams(window.location.search)
  const formNo: string = query.get('form_no') || ''
  const session: string = query.get('session') || ''

  const { data: lookupData } = useQuery({
    queryKey: ['hello'],
    queryFn: async () => {
      const res = await fetch(ApiUtils.getApiUrl('/api/third-party'), {
        method: 'POST',
        body: JSON.stringify({ form_no: formNo, session }),
      })
      return res.json()
    },
  })


  return (
    <>
      jkhjkhjhk
    </>
  )
}

export default App
