FROM node:16-alpine AS base
WORKDIR /app

FROM base AS deps
COPY package.json yarn.lock .
RUN yarn install --production

FROM base AS build
COPY --from=deps /app/node_modules ./node_modules
COPY . .
RUN yarn build

FROM base AS server
RUN yarn global add serve
COPY --from=build /app/build ./
CMD serve -s build
