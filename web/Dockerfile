FROM node:24-alpine

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm install

COPY . .

EXPOSE 3000

CMD ["sh", "-c", "npm run seed && npm run dev -- --hostname 0.0.0.0"]
