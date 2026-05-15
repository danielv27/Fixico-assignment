import { NextResponse, type NextRequest } from "next/server";
import { USER_COOKIE } from "@/lib/users/constants";

export function proxy(request: NextRequest) {
  if (request.cookies.has(USER_COOKIE)) {
    return NextResponse.next();
  }

  const userId = crypto.randomUUID();
  const requestHeaders = new Headers(request.headers);
  const cookie = requestHeaders.get("cookie");
  requestHeaders.set(
    "cookie",
    cookie ? `${cookie}; ${USER_COOKIE}=${userId}` : `${USER_COOKIE}=${userId}`,
  );

  const response = NextResponse.next({
    request: {
      headers: requestHeaders,
    },
  });
  response.cookies.set(USER_COOKIE, userId, {
    path: "/",
    httpOnly: false,
    maxAge: 60 * 60 * 24 * 365,
    sameSite: "lax",
  });

  return response;
}

export const config = {
  matcher: ["/", "/:path*"],
};
