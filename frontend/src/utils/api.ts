export class ApiUtils {
  static getApiUrl(path: string): string {
    return (import.meta.env.VITE_API_URL || 'http://localhost:8080') + path
  }
}